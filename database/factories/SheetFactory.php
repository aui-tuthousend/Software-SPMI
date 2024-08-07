<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Sheet>
 */
class SheetFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            //
            'jurusan' => $this->faker->word(),
            'periode' => $this->faker->date(),
            'note' => $this->faker->paragraph(),
            'tipe_sheet' => $this->faker->randomElement(['pendidikan', 'pengabdian', 'penelitian'])
        ];
    }
}
