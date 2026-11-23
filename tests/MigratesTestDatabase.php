<?php

namespace Tests;

use Illuminate\Support\Facades\DB;

trait MigratesTestDatabase
{
    protected function setUp(): void
    {
        parent::setUp();

        $connection = DB::connection();
        $this->assertSame('sqlite', $connection->getDriverName());
        $this->assertSame(':memory:', $connection->getDatabaseName());
        $this->artisan('migrate')->assertExitCode(0);
    }
}
