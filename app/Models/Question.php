<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'text',
        'enable_synonyms',
        'type',
        'level'
    ];

    public function tests(): BelongsToMany
    {
        return $this->belongsToMany(Test::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class);
    }
}
