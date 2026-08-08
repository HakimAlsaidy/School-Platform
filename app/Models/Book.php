<?php

namespace App\Models;

use App\Models\Traits\UsesSchoolSchema;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Book extends Model
{
    use HasFactory, UsesSchoolSchema;

    protected $fillable = [
        'school_id',
        'title',
        'author',
        'isbn',
        'publisher',
        'category',
        'total_copies',
        'available_copies',
        'shelf_location',
        'description',
    ];

    public function loans(): HasMany
    {
        return $this->hasMany(BookLoan::class);
    }

    public function getIsAvailableAttribute(): bool
    {
        return $this->available_copies > 0;
    }
}
