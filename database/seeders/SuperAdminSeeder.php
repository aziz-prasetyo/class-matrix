<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Muhammad Aziz Prasetyo',
            'email' => '2110511095@mahasiswa.upnvj.ac.id',
            'identity_number' => '2110511095',
        ]);

        User::factory()->create([
            'name' => 'Super Administrator',
            'email' => 'superadmin@example.test',
            'identity_number' => '0886410922',
        ]);
    }
}
