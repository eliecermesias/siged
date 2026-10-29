<?php

namespace App\Models;

use Database\Factories\PeopleFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class People extends Model
{
    /** @use HasFactory<PeopleFactory> */
    use HasFactory;

    protected $fillable = [
        'document_type_id',
        'document',
        'first_name',
        'last_name',
        'email',
        'phone',
        'address',
        'birth_date',
    ];

    /**
     * Get the document type assigned to the person.
     *
     * @return BelongsTo<DocumentTypes, $this>
     */
    public function documentType(): BelongsTo
    {
        return $this->belongsTo(DocumentTypes::class, 'document_type_id');
    }

    /**
     * Get the teacher record assigned to this person.
     *
     * @return HasOne<Teachers, $this>
     */
    public function teacher(): HasOne
    {
        return $this->hasOne(Teachers::class, 'person_id');
    }

    /**
     * Get the student record assigned to this person.
     *
     * @return HasOne<Students, $this>
     */
    public function student(): HasOne
    {
        return $this->hasOne(Students::class, 'person_id');
    }

    /**
     * Get the coordinator record assigned to this person.
     *
     * @return HasOne<Coordinators, $this>
     */
    public function coordinator(): HasOne
    {
        return $this->hasOne(Coordinators::class, 'person_id');
    }

    /**
     * Get the role label for the person, assuming role relationships are eager loaded.
     */
    public function roleLabel(): string
    {
        $roles = collect([
            'Docente' => $this->teacher !== null,
            'Estudiante' => $this->student !== null,
            'Administrativo' => $this->coordinator !== null,
        ])->filter()->keys();

        return match ($roles->count()) {
            0 => 'Sin rol',
            1 => $roles->first(),
            default => 'Múltiples',
        };
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
        ];
    }
}
