<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Designation extends Model
{
    protected $primaryKey = 'designation_id';

    protected $fillable = [
        'name',
        'slug',
    ];

    /**
     * Faculty and staff members with this primary designation.
     */
    public function members(): HasMany
    {
        return $this->hasMany(FacultyMember::class, 'designation_id', 'designation_id');
    }
}
