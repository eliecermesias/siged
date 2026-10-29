<?php

namespace App\Models;

use Database\Factories\TeachersFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Teachers extends Model
{
    /** @use HasFactory<TeachersFactory> */
    use HasFactory;

    protected $fillable = [
        'person_id',
        'code',
        'is_active',
    ];

    /**
     * Get the person assigned to this teacher record.
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
