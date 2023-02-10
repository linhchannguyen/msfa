<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // $seederVer = env('SEEDER_VERSION');
        // $functionName = 'seeder__' . str_replace('-', '_', $seederVer);
        // $this->$functionName();
    }

    protected function seeder__2021_01_11()
    {
        $this->call([
            MessageSeeder::class,
        ]);
    }
}
