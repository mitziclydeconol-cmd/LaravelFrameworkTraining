<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'display_name'];

    const STUDENT    = 'student';
    const INSTRUCTOR = 'instructor';

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
