<?php

use App\Models\People;

it('creates a person with a valid document type using its factory', function () {
    $person = People::factory()->create();

    $this->assertModelExists($person);
    $this->assertDatabaseHas('document_types', [
        'id' => $person->document_type_id,
    ]);
});
