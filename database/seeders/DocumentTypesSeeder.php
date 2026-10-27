<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class DocumentTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (! Schema::hasTable('document_types')) {
            return;
        }

        $documentTypes = [
            [
                'name' => 'Tarjeta de Identidad',
            ],
            [
                'name' => 'Cedula de Ciudadania',
            ],
            [
                'name' => 'Cedula de Extranjeria',
            ],
            [
                'name' => 'Pasaporte',
            ],
            [
                'name' => 'Registro Civil',
            ],
            [
                'name' => 'Permiso Especial de Permanencia',
            ],
            [
                'name' => 'Documento Nacional de Identidad',
            ],
            [
                'name' => 'NIT',
            ],
            [
                'name' => 'Otro',
            ],
        ];

        foreach ($documentTypes as $type) {
            \DB::table('document_types')->insert($type);
        }
    }
}
