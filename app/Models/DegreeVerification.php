<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DegreeVerification extends Model
{
    public $timestamps = false;

    protected $primaryKey = 'scroll_id';

    protected $fillable = [
        'degree_id',
        'reg_no',
        'st_name',
        'st_fname',
        'degree_name',
        'subject',
        'level',
        'year',
    ];

    /** Degree level codes stored in the database. */
    public const LEVELS = [
        0 => 'Undergraduate',
        1 => 'Post Graduate',
        2 => 'PhD',
        3 => 'Duplicate',
        4 => 'Revised',
    ];
}
