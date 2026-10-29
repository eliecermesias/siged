<?php

namespace App\Models;

use Database\Factories\StudentsFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Students extends Model
{
    /** @use HasFactory<StudentsFactory> */
    use HasFactory;

    protected $fillable = [
        'person_id',
        'code',
        'is_active',
    ];

    /**
     * Get the person assigned to this student record.
     *
     * @return BelongsTo<People, $this>
     */
    public function person(): BelongsTo
    {
        return $this->belongsTo(People::class, 'person_id');
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }
}
