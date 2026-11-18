<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $count = User::count();

        for ($i = 1; $i <= 5 && $count < 5; $i++) {
            $email = 'player'.$i.'@example.test';

            if (!User::where('email', $email)->exists()) {
                User::factory()->create([
                    'name' => 'Игрок '.$i,
                    'email' => $email,
                ]);
                $count++;
            }
        }
    }
}
