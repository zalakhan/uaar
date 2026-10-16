<?php



namespace App\Models;



use Illuminate\Database\Eloquent\Builder;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;



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



    public function thumbnail(): HasOne

    {

        return $this->hasOne(GalleryPhoto::class)->where('sort_order', 0);

    }



    /**

     * Whether this gallery already has a photo marked as the thumbnail.

     */

    public function hasThumbnailPhoto(): bool

    {

        return $this->photos()->where('sort_order', 0)->exists();

    }



    /**

     * Thumbnail photo (sort_order 0), or the first photo when none is set.

     */

    public function resolveThumbnailPhoto(): ?GalleryPhoto

    {

        $thumbnail = $this->relationLoaded('thumbnail')

            ? $this->thumbnail

            : $this->thumbnail()->first();



        if ($thumbnail) {

            return $thumbnail;

        }



        if ($this->relationLoaded('photos')) {

            return $this->photos->first();

        }



        return $this->photos()->orderBy('sort_order')->orderBy('id')->first();

    }



    /**

     * Public thumbnail URL (same format as gallery photo URLs in the API).

     */

    public function thumbnailUrl(): ?string

    {

        $photo = $this->resolveThumbnailPhoto();



        if (! $photo?->photo) {

            return null;

        }



        return asset('storage/'.$photo->photo);

    }

}

