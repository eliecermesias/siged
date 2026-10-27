<?php

namespace Database\Factories;

use App\Models\DocumentTypes;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DocumentTypes>
 */
class DocumentTypesFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->randomElement([
                'Tarjeta de Identidad',
                'Cedula de Ciudadania',
                'Cedula de Extranjeria',
                'Pasaporte',
                'Registro Civil',
                'Permiso Especial de Permanencia',
                'Documento Nacional de Identidad',
                'NIT',
                'Otro',
            ]),
        ];
    }
}
