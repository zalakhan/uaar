<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Job extends Model
{
    protected $table = 'research_jobs';

    protected $fillable = [
        'title',
        'department_id',
        'last_date',
        'job_file',
    ];

    protected function casts(): array
    {
        return [
            'last_date' => 'date',
        ];
    }

    /**
     * Scope records to the logged-in user's department when applicable.
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
}
