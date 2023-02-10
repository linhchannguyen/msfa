<?php

namespace Database\Factories;

abstract class BaseFactory
{
    protected $faker;

    public function __construct()
    {
        $this->faker = Faker\Factory::create();
    }

    public function prepare(int $count = 1)
    {
        $data = [];

        for($i = 0; $i < $count; $i++) {
            array_push($data, $this->definitions());
        }

        return $data;
    }

    abstract function definitions();
}