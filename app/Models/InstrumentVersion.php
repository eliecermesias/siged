<?php

namespace App\Models;

use Database\Factories\InstrumentVersionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InstrumentVersion extends Model
{
    /** @use HasFactory<InstrumentVersionFactory> */
    use HasFactory;

    protected $fillable = [
        'evaluation_instrument_id',
        'version_number',
        'notes',
        'is_active',
    ];

    /**
     * Get the instrument this version belongs to.
     *
     * @return BelongsTo<EvaluationInstrument, $this>
     */
    public function evaluationInstrument(): BelongsTo
    {
        return $this->belongsTo(EvaluationInstrument::class);
    }

    /**
     * Get the characteristics in this version.
     *
     * @return HasMany<Characteristics, $this>
     */
    public function characteristics(): HasMany
    {
        return $this->hasMany(Characteristics::class);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'version_number' => 'integer',
            'is_active' => 'boolean',
        ];
    }
}
