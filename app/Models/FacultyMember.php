<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FacultyMember extends Model
{
    protected $fillable = [
        'department_id',
        'faculty_id',
        'member_type',
        'name',
        'designation',
        'email',
        'phone',
        'mobile',
        'qualification',
        'bio',
        'address',
        'total_experience',
        'total_publication',
        'is_hec',
        'sort_order',
        'is_studyleave',
        'is_onleave',
        'is_active',
        'additional_department',
        'additional_designation',
        'photo',
    ];

    protected function casts(): array
    {
        return [
            'is_hec' => 'boolean',
            'is_studyleave' => 'boolean',
            'is_onleave' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Limit query to faculty-type members only.
     * Named facultyType to avoid clashing with the faculty() relationship.
     */
    public function scopeFacultyType(Builder $query): Builder
    {
        return $query->where('member_type', 'faculty');
    }

    /**
     * Limit query to staff-type members only.
     */
    public function scopeStaffType(Builder $query): Builder
    {
        return $query->where('member_type', 'staff');
    }

    /**
     * Scope records to the logged-in user's department (dept_admin only on listings).
     * Staff and super_admin see all departments.
     */
    public function scopeForUser(Builder $query, User $user, bool $forListing = false): Builder
    {
        if ($user->isSuperAdmin()) {
            return $query;
        }

        if ($forListing && $user->hasRole('staff')) {
            return $query;
        }

        return $query->where('department_id', $user->department_id);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function faculty(): BelongsTo
    {
        return $this->belongsTo(Faculty::class);
    }

    /**
     * Publications for this faculty member.
     */
    public function publications(): HasMany
    {
        return $this->hasMany(Publication::class);
    }

    /**
     * Awards for this faculty member.
     */
    public function awards(): HasMany
    {
        return $this->hasMany(Award::class);
    }
}
