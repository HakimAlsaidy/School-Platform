<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug'];

    const ADMIN = 'admin';
    const TEACHER = 'teacher';
    const PARENT = 'parent';

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
