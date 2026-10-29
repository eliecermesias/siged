<?php

namespace App\Models;

use Database\Factories\EvaluationInstrumentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EvaluationInstrument extends Model
{
    /** @use HasFactory<EvaluationInstrumentFactory> */
    use HasFactory;

    protected $fillable = [
        'intention_id',
        'evaluation_modality_id',
        'name',
        'description',
        'is_active',
    ];

    /**
     * Get the intention this instrument serves.
     *
     * @return BelongsTo<Intention, $this>
     */
    public function intention(): BelongsTo
    {
        return $this->belongsTo(Intention::class);
    }

    /**
     * Get the evaluation modality this instrument uses.
     *
     * @return BelongsTo<EvaluationModality, $this>
     */
    public function evaluationModality(): BelongsTo
    {
        return $this->belongsTo(EvaluationModality::class);
    }

    /**
     * Get the versions of this instrument.
     *
     * @return HasMany<InstrumentVersion, $this>
     */
    public function versions(): HasMany
    {
        return $this->hasMany(InstrumentVersion::class);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }
}
