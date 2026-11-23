<?php

namespace App\Support;

use RuntimeException;

final class WordPressDump
{
    private $file;
    private $prefix;
    private $seen = [];

    public function __construct(string $file, string $prefix = 'wp_')
    {
        $this->file = $file;
        $this->prefix = $prefix;
    }

    public function rows(array $tables): iterable
    {
        $handle = fopen($this->file, 'rb');
        if (!$handle) {
            throw new RuntimeException('Dump WordPress tidak dapat dibaca.');
        }

        $wanted = array_flip($tables);
        $prefix = preg_quote($this->prefix, '/');
        try {
            while (($line = fgets($handle)) !== false) {
                if (!preg_match('/^INSERT INTO `' . $prefix . '([a-z0-9_]+)` \(([^)]+)\) VALUES\s*/i', $line, $match)
                    || !isset($wanted[$match[1]])) {
                    continue;
                }

                $table = $match[1];
                $this->seen[$table] = true;
                $columns = array_map(function ($column) {
                    return trim($column, " `");
                }, explode(',', $match[2]));
                $statement = substr($line, strlen($match[0]));
                while (!preg_match('/\)\s*;\s*$/D', $statement)) {
                    $next = fgets($handle);
                    if ($next === false || strlen($statement) + strlen($next) > 32 * 1024 * 1024) {
                        throw new RuntimeException('Blok INSERT tidak lengkap atau terlalu besar: ' . $table);
                    }
                    $statement .= $next;
                }

                foreach ($this->tuples($statement, count($columns)) as $values) {
                    yield [$table, array_combine($columns, $values)];
                }
            }
        } finally {
            fclose($handle);
        }

        if (array_diff($tables, array_keys($this->seen))) {
            throw new RuntimeException('Dump tidak memiliki semua tabel INSERT yang diperlukan.');
        }
    }

    private function tuples(string $sql, int $columns): iterable
    {
        $i = 0;
        do {
            $this->consume($sql, $i, '(');
            $values = [];
            for ($field = 0; $field < $columns; $field++) {
                if ($field) {
                    $this->consume($sql, $i, ',');
                }
                $i += strspn($sql, " \r\n\t", $i);
                if (($sql[$i] ?? '') === "'") {
                    $values[] = $this->readString($sql, $i);
                } elseif (preg_match('/\G(?:NULL|-?\d+(?:\.\d+)?(?:e[+-]?\d+)?)(?=[\s,)])/i', $sql, $match, 0, $i)) {
                    $values[] = strtoupper($match[0]) === 'NULL' ? null : $match[0];
                    $i += strlen($match[0]);
                } else {
                    throw new RuntimeException('Nilai INSERT WordPress tidak dikenali.');
                }
            }
            $this->consume($sql, $i, ')');
            yield $values;
            $i += strspn($sql, " \r\n\t", $i);
        } while (($sql[$i++] ?? '') === ',');

        if (($sql[$i - 1] ?? '') !== ';' || trim(substr($sql, $i)) !== '') {
            throw new RuntimeException('Penutup blok INSERT WordPress tidak valid.');
        }
    }

    private function consume(string $sql, int &$i, string $token): void
    {
        $i += strspn($sql, " \r\n\t", $i);
        if (($sql[$i++] ?? '') !== $token) {
            throw new RuntimeException('Struktur INSERT WordPress tidak valid.');
        }
    }

    private function readString(string $sql, int &$i): string
    {
        $value = '';
        $i++;
        $size = strlen($sql);
        $escapes = ['0' => "\0", 'b' => "\x08", 'n' => "\n", 'r' => "\r", 't' => "\t", 'Z' => "\x1a"];
        while ($i < $size) {
            $part = strcspn($sql, "'\\", $i);
            $value .= substr($sql, $i, $part);
            $i += $part;
            $next = $sql[$i + 1] ?? '';
            if (($sql[$i] ?? '') === "'") {
                if ($next !== "'") {
                    $i++;
                    return $value;
                }
                $value .= "'";
            } elseif (($sql[$i] ?? '') === "\\" && $next !== '') {
                $value .= $escapes[$next] ?? $next;
            } else {
                break;
            }
            $i += 2;
        }

        throw new RuntimeException('Teks dalam INSERT WordPress tidak ditutup.');
    }
}
