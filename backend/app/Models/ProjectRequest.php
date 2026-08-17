<?php

namespace App\Models;

use App\Enums\ProjectRequestStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectRequest extends Model
{
    use HasFactory;
         
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'budget',
        'currency',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'budget' => 'decimal:2',
            'status' => ProjectRequestStatus::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
