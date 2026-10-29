<?php

namespace App\Models;

use Database\Factories\AcademicProgramsFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AcademicPrograms extends Model
{
    /** @use HasFactory<AcademicProgramsFactory> */
    use HasFactory;

    protected $fillable = [
        'education_level_id',
        'knowledge_area_id',
        'name',
        'description',
        'snies',
        'resolution',
        'is_active',
    ];

    /**
     * Get the formation level assigned to this program.
     *
     * @return BelongsTo<EducationLevel, $this>
     */
    public function educationLevel(): BelongsTo
    {
        return $this->belongsTo(EducationLevel::class);
    }

    /**
     * Get the knowledge area assigned to this program.
     *
     * @return BelongsTo<KnowledgeArea, $this>
     */
    public function knowledgeArea(): BelongsTo
    {
        return $this->belongsTo(KnowledgeArea::class);
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
