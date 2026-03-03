<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class NasFactory extends Factory
{
    public function definition()
    {
        return [
            'name' => $this->faker->word(),
            'ip_address' => $this->faker->ipv4(),
            'secret' => Str::random(16),
            'model' => 'RouterOS',
            'api_type' => 'routeros',
            'tenant_id' => null,
            'admin_id' => null,
            'operator_id' => null,
        ];
    }
}
