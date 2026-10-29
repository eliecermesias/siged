<?php

namespace App\Actions\Students;

use App\Models\People;
use App\Models\Students;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CreateStudent
{
    /**
     * Assign an existing person to a student record.
     */
    public function assign(People $person, string $errorField = 'person_id'): Students
    {
        return DB::transaction(function () use ($person, $errorField): Students {
            $person = People::query()
                ->lockForUpdate()
                ->findOrFail($person->getKey());

            if ($person->student()->exists()) {
                throw ValidationException::withMessages([
                    $errorField => 'Esta persona ya está asociada a un estudiante.',
                ]);
            }

            return $this->createStudentFor($person);
        });
    }

    /**
     * Create a person and assign it to a student in one transaction.
     *
     * @param  array{document_type_id: int, document: string, first_name: string, last_name: string, email: string, phone: ?string}  $attributes
     */
    public function createWithPerson(array $attributes): Students
    {
        return DB::transaction(function () use ($attributes): Students {
            $person = People::create($attributes);

            return $this->createStudentFor($person);
        });
    }

    private function createStudentFor(People $person): Students
    {
        do {
            $code = 'E'.Str::upper(Str::random(9));
        } while (Students::query()->where('code', $code)->exists());

        return Students::create([
            'person_id' => $person->id,
            'code' => $code,
            'is_active' => true,
        ]);
    }
}
