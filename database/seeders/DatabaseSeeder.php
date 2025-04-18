<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use App\Models\User;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        Artisan::call('module:migrate LandingPage');
        Artisan::call('module:seed LandingPage');
        $this->call(NotificationSeeder::class);


            $this->call(UsersTableSeeder::class);
            $this->call(AiTemplateSeeder::class);


            User::seed_languages();


    }
}
