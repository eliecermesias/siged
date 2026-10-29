<?php

namespace App\Actions\Coordinators;

use App\Models\Coordinators;
use App\Models\People;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CreateCoordinator
{
    public function assign(People $person, string $errorField = 'person_id'): Coordinators
    {
        return DB::transaction(function () use ($person, $errorField): Coordinators {
            $person = People::query()
                ->lockForUpdate()
                ->findOrFail($person->getKey());

            if ($person->coordinator()->exists()) {
                throw ValidationException::withMessages([
                    $errorField => 'Esta persona ya está asociada a un coordinador.',
                ]);
            }

            return $this->createCoordinatorFor($person);
        });
    }

    /**
     * @param  array{document_type_id: int, document: string, first_name: string, last_name: string, email: string, phone: ?string}  $attributes
     */
    public function createWithPerson(array $attributes): Coordinators
    {
        return DB::transaction(function () use ($attributes): Coordinators {
            $person = People::create($attributes);

            return $this->createCoordinatorFor($person);
        });
    }

    private function createCoordinatorFor(People $person): Coordinators
    {
        do {
            $code = 'C'.Str::upper(Str::random(9));
        } while (Coordinators::query()->where('code', $code)->exists());

        return Coordinators::create([
            'person_id' => $person->id,
            'code' => $code,
            'is_active' => true,
        ]);
    }
}
