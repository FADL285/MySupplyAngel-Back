<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name'  => "Supper Admin",
            'phone' => "01000000000",
            'email' => "admin@info.com",
            'is_active' => 1,
            'is_ban' => 0,
            'email_verified_at' => now(),
            'phone_verified_at' => now(),
            'password' => Hash::make('123456789'), // $2y$10$LrT8sFunr5i9qmQKC/umN.jz8rPNi9OkmgU92kXT571W21R5n3jUy
            'user_type' => 'superadmin',
            'gender' => 'male',
            'remember_token' => Str::random(10),
        ]);
    }
}
