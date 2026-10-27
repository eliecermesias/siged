<?php

namespace App\Models;

use Database\Factories\DocumentTypesFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DocumentTypes extends Model
{
    /** @use HasFactory<DocumentTypesFactory> */
    use HasFactory;

    /**
     * Get the people with this document type.
     *
     * @return HasMany<People, $this>
     */
    public function people(): HasMany
    {
        return $this->hasMany(People::class, 'document_type_id');
    }
}
