<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, HasRoles, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'department_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Department this user belongs to (null for super_admin).
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Check if user is the web manager with full access.
     */
    public function isSuperAdmin(): bool
    {
        return $this->hasRole('super_admin');
    }

    public function isDepartmentAdmin(): bool
    {
        return $this->hasRole('dept_admin');
    }

    /**
     * Whether this user may open the Event Registrations module in the sidebar.
     */
    public function canAccessEventRegistrationsModule(): bool
    {
        if ($this->isSuperAdmin() || $this->can('event_registrations.view')) {
            return true;
        }

        return $this->isDepartmentAdmin() && $this->department_id !== null;
    }

    /**
     * Registrations for an event: Super Admin, or dept_admin of the organizing department.
     */
    public function canViewRegistrationsForEvent(Event $event): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        if (! $this->isDepartmentAdmin()) {
            return false;
        }

        if ($this->department_id === null || $event->department_id === null) {
            return false;
        }

        return (int) $this->department_id === (int) $event->department_id;
    }
}
