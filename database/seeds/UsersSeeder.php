<?php

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        User::factory(100)->hasPosts(rand(5,20))->create();
        User::create([
            "name" => "Super Admin",
            "email" => "admin@admin.com",
            "password" => Hash::make('password'),
            "email_verified_at" => now(),
            "role" => 'admin', // Admin
        ]);
    }
}
