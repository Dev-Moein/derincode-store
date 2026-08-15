<?php

namespace App\Models;

use App\Enums\ProjectStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'short_description',
        'description',
        'price',
        'currency',
        'is_for_sale',
        'is_featured',
        'status',
        'file_path',
        'file_name',
        'file_size',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'is_for_sale' => 'boolean',
            'is_featured' => 'boolean',
            'published_at' => 'datetime',
            'status' => ProjectStatus::class,
        ];
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProjectImage::class);
    }

    public function projectRequests(): HasMany
    {
        return $this->hasMany(ProjectRequest::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function downloads(): HasMany
    {
        return $this->hasMany(Download::class);
    }
}
