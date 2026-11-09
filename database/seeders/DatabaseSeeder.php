<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Popula o banco para a demonstração (php artisan migrate:fresh --seed).
     */
    public function run(): void
    {
        $this->call(DemoSeeder::class);
    }
}
