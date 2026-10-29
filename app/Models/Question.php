<?php

namespace App\Models;

use Database\Factories\QuestionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Question extends Model
{
    /** @use HasFactory<QuestionFactory> */
    use HasFactory;

    protected $fillable = [
        'characteristic_id',
        'question_text',
        'response_type',
        'is_required',
        'position',
        'is_active',
    ];

    /**
     * Get the characteristic this question belongs to.
     *
     * @return BelongsTo<Characteristics, $this>
     */
    public function characteristics(): BelongsTo
    {
        return $this->belongsTo(Characteristics::class, 'characteristic_id');
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_required' => 'boolean',
            'position' => 'integer',
            'is_active' => 'boolean',
        ];
    }
}
