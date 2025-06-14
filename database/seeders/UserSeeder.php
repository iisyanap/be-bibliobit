<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Hapus user admin lama untuk memastikan tidak ada duplikat
        User::where('email', 'admin@bibliobit.com')->delete();

        // Buat user admin baru dengan password yang di-hash dengan benar
        User::create([
            'name' => 'Admin Bibliobit',
            'email' => 'isyanap@gmail.com',
            'password' => Hash::make('1234'), // Menggunakan Hash::make() adalah cara yang benar
        ]);
    }
}
