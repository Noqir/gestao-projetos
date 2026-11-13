<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Popula o banco para a demonstração (php artisan migrate:fresh --seed).
     *
     * Só roda com o banco vazio: na hospedagem o seed acontece a cada deploy
     * (o Railpack roda migrate e seed ao iniciar) e não pode duplicar nem apagar dados existentes.
     */
    public function run(): void
    {
        if (User::query()->exists()) {
            $this->command?->info('Banco já possui dados; seed de demonstração ignorado.');

            return;
        }

        $this->call(DemoSeeder::class);
    }
}
