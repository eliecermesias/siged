<?php

use App\Models\People;
use Database\Seeders\DocumentTypesSeeder;
use Database\Seeders\PeopleSeeder;

test('the people seed maintains forty people without duplicating document types', function () {
    app(DocumentTypesSeeder::class)->run();

    app(PeopleSeeder::class)->run();
    app(PeopleSeeder::class)->run();

    $this->assertDatabaseCount('people', 40);
    $this->assertDatabaseCount('document_types', 9);

    expect(People::query()->distinct('email')->count('email'))->toBe(40);
});
