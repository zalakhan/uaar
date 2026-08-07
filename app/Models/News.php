<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class News extends Model
{
    public const MAIN_WEBSITE_SCOPE = 'main';

    protected $fillable = [
        'title',
        'description',
        'published_date',
        'image',
        'slug',
        'status',
        'department_id',
    ];

    protected function casts(): array
    {
        return [
            'published_date' => 'date',
            'status' => 'boolean',
        ];
    }

    /**
     * Whether the news article is active (visible).
     */
    public function isActive(): bool
    {
        return (bool) $this->status;
    }

    /**
     * Sanitized HTML content safe for rendering.
     */
    public function sanitizedDescription(): string
    {
        return clean($this->description ?? '', 'news');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Display label for the news department scope.
     */
    public function departmentLabel(): string
    {
        return $this->department?->name ?? 'Main Website';
    }

    /**
     * Form select value for the current department scope.
     */
    public function departmentScopeValue(): string
    {
        return $this->department_id ? (string) $this->department_id : self::MAIN_WEBSITE_SCOPE;
    }
}
