<?php

namespace Database\Factories;

use App\Models\Contact;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContactFactory extends Factory
{
    protected $model = Contact::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'email' => FactoryConstants::CONTACT_EMAIL,
            'phone' => $this->faker->phoneNumber,
            'lead_source' => $this->faker->userName,
            'salesforce_id' => null,
            'is_synced' => false
        ];
    }

    /**
     * @param bool $sync
     * @return ContactFactory
     */
    public function setSync(bool $sync): ContactFactory
    {
        return $this->state([
            'is_synced' => $sync
        ]);
    }

    /**
     * @param string $field
     * @param $value
     * @return ContactFactory
     */
    public function withField(string $field, $value): ContactFactory
    {
        return $this->state([
            $field => $value
        ]);
    }

}
