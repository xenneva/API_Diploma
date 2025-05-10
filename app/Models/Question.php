<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'text',
        'answer',
        'enable_synonyms'
    ];

    public function tests(): BelongsToMany
    {
        return $this->belongsToMany(Test::class);
    }
}
