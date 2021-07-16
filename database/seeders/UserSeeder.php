<?php

namespace Database\Seeders;

use App\Models\User;
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

        $users = [
            [
                'name' => 'Admin',
                'email' => 'admin@admin.com',
                'password' => Hash::make("Admin@123"),
                'admin' => 1
            ],
            [
                'name' => 'User',
                'email' => 'user@user.com',
                'password' => Hash::make("User@123"),
                'admin' => 0
            ]
        ];

        foreach ($users as $rawUser) {
            $user = User::create($rawUser);
            $user->createToken('forum-app')->plainTextToken;
        }

    }
}
