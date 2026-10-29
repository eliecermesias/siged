<?php

namespace App\Models;

use Database\Factories\IntentionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Intention extends Model
{
    /** @use HasFactory<IntentionFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'is_active',
    ];

    /**
     * Get the instruments that use this intention.
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
