<?php



namespace App\Models;



use Illuminate\Database\Eloquent\Builder;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

use Illuminate\Database\Eloquent\Relations\HasMany;



class Gallery extends Model

{

    protected $fillable = [

        'name',

        'date',

        'department_id',

        'status',

        'slug',

    ];



    protected function casts(): array

    {

        return [

            'date' => 'date',

            'status' => 'boolean',

        ];

    }



    /**

     * Whether the gallery is active (visible).

     */

    public function isActive(): bool

    {

        return (bool) $this->status;

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



    public function photos(): HasMany

    {

        return $this->hasMany(GalleryPhoto::class)->orderBy('sort_order')->orderBy('id');

    }

}

