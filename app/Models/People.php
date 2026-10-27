<?php

namespace App\Models;

use Database\Factories\PeopleFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
