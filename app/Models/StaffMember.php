<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;

/**
 * Staff members share the faculty_members table with member_type = staff.
 */
class StaffMember extends FacultyMember
{
    protected $table = 'faculty_members';

    protected static function booted(): void
    {
        static::addGlobalScope('staff', function (Builder $builder) {
            $builder->where('member_type', 'staff');
        });

        static::creating(function (StaffMember $member) {
            $member->member_type = 'staff';
        });
    }
}
