<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Newspaper extends Model
{
    protected $fillable = [
        'newspaper_name',
    ];

    public function newsPrints(): HasMany
    {
        return $this->hasMany(NewsPrint::class);
    }
}
