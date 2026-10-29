<?php

namespace App\Models;

use Database\Factories\EvaluationModalityFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EvaluationModality extends Model
{
    /** @use HasFactory<EvaluationModalityFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'is_active',
    ];

    /**
     * Get the instruments that use this modality.
     *
     * @return HasMany<EvaluationInstrument, $this>
     */
    public function evaluationInstruments(): HasMany
    {
        return $this->hasMany(EvaluationInstrument::class);
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
