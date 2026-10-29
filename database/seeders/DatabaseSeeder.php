<?php
namespace Database\Seeders;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Md. Rafizul Islam',
            'email' => 'admin@farazy.com',
            'password' => Hash::make('password123'),
        ]);
    }
}
