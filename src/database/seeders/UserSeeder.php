<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        try {
            User::create([
                'role'              => 'admin',
                'name'              => 'Admin',
                'email'             => 'admin@mydomain.com',
                'country_code'      => '+57',
                'phone_number'      => '3133929826',
                'full_phone_number' => '+573133929826',
                'password'          => '$2y$10$dmQmyyu./5uEb.Ti/ZeO3e80V8.mbivA4K1b43O9yvjWbvff0J7qK'
            ]);

        } catch (\Throwable $e) {
            Log::info(get_class() . $e->getMessage());
        }
    }
}
