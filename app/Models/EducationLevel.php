<?php

namespace App\Models;

use Database\Factories\EducationLevelFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EducationLevel extends Model
{
    /** @use HasFactory<EducationLevelFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'is_active',
    ];

    /**
     * Get the academic programs that use this formation level.
     *
     * @return HasMany<AcademicPrograms, $this>
     */
    public function academicPrograms(): HasMany
    {
        return $this->hasMany(AcademicPrograms::class);
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
