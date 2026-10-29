<?php

namespace App\Models;

use Database\Factories\CharacteristicsFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Characteristics extends Model
{
    /** @use HasFactory<CharacteristicsFactory> */
    use HasFactory;

    protected $fillable = [
        'instrument_version_id',
        'name',
        'description',
        'position',
        'is_active',
    ];

    /**
     * Get the version that contains this characteristic.
     *
     * @return BelongsTo<InstrumentVersion, $this>
     */
    public function instrumentVersion(): BelongsTo
    {
        return $this->belongsTo(InstrumentVersion::class);
    }

    /**
     * Get the questions grouped under this characteristic.
     *
     * @return HasMany<Question, $this>
     */
    public function questions(): HasMany
    {
        return $this->hasMany(Question::class, 'characteristic_id');
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'position' => 'integer',
            'is_active' => 'boolean',
        ];
    }
}
