<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Setting;
use App\Support\HomeLayout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class HomeLayoutController extends Controller
{
    public function index()
    {
        $saved = HomeLayout::saved();

        return view('admin.home-layout', [
            'blocks' => $saved ?? HomeLayout::defaults(),
            'catalog' => HomeLayout::BLOCKS + HomeLayout::CUSTOM,
            'isCustom' => $saved !== null,
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate(['layout' => 'required|string|max:80000']);
        $layout = json_decode($data['layout'], true, 10);
        $rows = HomeLayout::ROWS;
        $limit = HomeLayout::LIMIT;
        $blocks = Validator::make(['blocks' => $layout], [
            'blocks' => "present|array|list|max:$limit",
            'blocks.*' => 'required|array:type,x,y,w,h,title,content,image,link',
            'blocks.*.type' => ['required', Rule::in(array_keys(HomeLayout::BLOCKS + HomeLayout::CUSTOM))],
            'blocks.*.x' => 'required|integer|min:1|max:12',
            'blocks.*.y' => "required|integer|min:1|max:$rows",
            'blocks.*.w' => 'required|integer|min:1|max:12',
            'blocks.*.h' => "required|integer|min:1|max:$rows",
            'blocks.*.title' => 'nullable|string|max:100',
            'blocks.*.content' => 'nullable|string|max:2000',
            'blocks.*.image' => 'nullable|string|max:400|url:http,https',
            'blocks.*.link' => 'nullable|string|max:400|url:http,https',
        ])->validate()['blocks'];

        $occupied = $used = [];
        $normalized = [];
        foreach ($blocks as $block) {
            ['type' => $type, 'x' => $x, 'y' => $y, 'w' => $w, 'h' => $h] = $block;
            if ($x + $w - 1 > 12 || $y + $h - 1 > $rows) {
                throw ValidationException::withMessages(['layout' => 'Ada blok yang melewati batas grid.']);
            }
            if (isset(HomeLayout::BLOCKS[$type], $used[$type])) {
                throw ValidationException::withMessages(['layout' => 'Setiap modul beranda bawaan hanya dapat digunakan sekali.']);
            }
            for ($row = $y; $row < $y + $h; $row++) {
                for ($column = $x; $column < $x + $w; $column++) {
                    if (isset($occupied[$row][$column])) {
                        throw ValidationException::withMessages(['layout' => 'Ada blok yang saling bertumpukan.']);
                    }
                    $occupied[$row][$column] = true;
                }
            }

            $used[$type] = true;
            $item = ['type' => $type, 'x' => (int) $x, 'y' => (int) $y, 'w' => (int) $w, 'h' => (int) $h];
            if (isset(HomeLayout::CUSTOM[$type])) {
                $item['title'] = trim($block['title'] ?? '');
            }
            if ($type === 'text') {
                $item['content'] = trim($block['content'] ?? '');
                if ($item['content'] === '') {
                    throw ValidationException::withMessages(['layout' => 'Isi blok Teks Bebas sebelum menyimpan.']);
                }
            } elseif ($type === 'banner') {
                if (empty($block['image']) || $item['title'] === '') {
                    throw ValidationException::withMessages(['layout' => 'Isi judul dan URL gambar untuk setiap banner.']);
                }
                $item['image'] = $block['image'];
                $item['link'] = $block['link'] ?? '';
            }
            $normalized[] = $item;
        }

        Setting::set(HomeLayout::KEY, json_encode($normalized, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR));

        return redirect()->route('admin.home-layout.index')->with('success', 'Tata letak baru sudah tampil di beranda.');
    }

    public function destroy()
    {
        Setting::where('key', HomeLayout::KEY)->delete();

        return redirect()->route('admin.home-layout.index')->with('success', 'Tata letak bawaan dipulihkan.');
    }
}
