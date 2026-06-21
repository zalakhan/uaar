<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Publication extends Model
{
    protected $fillable = [
        'description',
        'year',
        'faculty_member_id',
    ];

    /**
     * Faculty member this publication belongs to.
     */
    public function facultyMember(): BelongsTo
    {
        return $this->belongsTo(FacultyMember::class);
    }
}
