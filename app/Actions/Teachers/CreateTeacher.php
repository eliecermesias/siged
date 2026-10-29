<?php

namespace App\Actions\Teachers;

use App\Models\People;
use App\Models\Teachers;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CreateTeacher
{
    /**
     * Assign an existing person to a teacher record.
     */
    public function assign(People $person, string $errorField = 'person_id'): Teachers
    {
        return DB::transaction(function () use ($person, $errorField): Teachers {
            $person = People::query()
                ->lockForUpdate()
                ->findOrFail($person->getKey());

            if ($person->teacher()->exists()) {
                throw ValidationException::withMessages([
                    $errorField => 'Esta persona ya está asociada a un docente.',
                ]);
            }

            return $this->createTeacherFor($person);
        });
    }

    /**
     * Create a person and assign it to a teacher in one transaction.
     *
     * @param  array{document_type_id: int, document: string, first_name: string, last_name: string, email: string, phone: ?string}  $attributes
     */
    public function createWithPerson(array $attributes): Teachers
    {
        return DB::transaction(function () use ($attributes): Teachers {
            $person = People::create($attributes);

            return $this->createTeacherFor($person);
        });
    }

    private function createTeacherFor(People $person): Teachers
    {
        do {
            $code = 'T'.Str::upper(Str::random(9));
        } while (Teachers::query()->where('code', $code)->exists());

        return Teachers::create([
            'person_id' => $person->id,
            'code' => $code,
            'is_active' => true,
        ]);
    }
}
