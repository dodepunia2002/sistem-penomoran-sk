<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@dishub.go.id'],
            ['name' => 'Administrator', 'password' => bcrypt('admin123'), 'role' => 'admin']
        );
        User::firstOrCreate(
            ['email' => 'petugas@dishub.go.id'],
            ['name' => 'Petugas Dishub', 'password' => bcrypt('petugas123'), 'role' => 'petugas']
        );
    }
}
