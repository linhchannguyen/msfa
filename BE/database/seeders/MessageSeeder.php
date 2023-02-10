<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Repositories\Home\HomeRepository;
use App\Database\Factories\MessageFactory;

class MessageSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run(MessageFactory $factory, HomeRepository $repo)
    {
        $data = $factory->prepare(20);
        $repo->insertBulk($data);
    }
}
