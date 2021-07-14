<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Post::factory()
            ->count(rand(0,50))
            ->for(User::all()->random())
            ->state(new Sequence(
                [
                    'approved' => 1,
                    'approved_by' => 1,
                    'approved_at' => now()
                ],
                [
                    'approved' => 0,
                    'approved_by' => null,
                    'approved_at' => null
                ],
            ))
            ->create();
    }
}
