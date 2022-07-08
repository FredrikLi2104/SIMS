<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $super = User::create(['name' => 'Ali Almir', 'email' => 'webmaster@gdpr.se', 'password' => Hash::make('Alpha123@'), 'role' => 'super']);
        $admin = User::create(['name' => 'Fredrik Jonasson', 'email' => 'fredrik@jonacom.se', 'password' => Hash::make('GammaDelta423@'), 'role' => 'admin']);
    }
}
