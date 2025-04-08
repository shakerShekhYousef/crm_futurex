<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Workspace;
use App\Models\Utility;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $adminUser = User::create([
            'name' => 'Futurex',
            'email' => 'Futurex@example.com',
            'password' => Hash::make('FutureX@2025'),
            'type' => 'admin',
            'email_verified_at' => date('Y-m-d i:h:s'),
            'lang' => 'en',
        ]);

        User::defaultEmail();
        User::seed_languages();

    }
}


