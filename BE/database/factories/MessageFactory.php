<?php

namespace Database\Factories;

class MessageFactory extends BaseFactory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definitions()
    {
        return [
            'message_subject' => $this->faker->string(),
            'release_start_date' => $this->faker->date(),
            'release_end_date' => $this->faker->date(),
            'release_org_cd' => random_int(1,100),
            'sender_name' => $this->faker->name(),
            'message_contents' => $this->faker->realText(),
            'important_flag' => random_int(0,1),
            'last_update_datetime' => $this->faker->date(),
            'create_user_cd' => random_int(0,100)
        ];
    }
}
