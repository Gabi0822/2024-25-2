<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ticket extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'done',
        'priority',
    ];

    protected function casts(): array
    {
        return [
            'done' => 'boolean',
        ];
    }

    public function comments(): HasMany {
        return $this->hasMany(Comment::class);
    }

    public function users(): BelongsToMany {
        return $this->belongsToMany(User::class);
    }

    function owner(): BelongsToMany {
        return $this->belongsToMany(User::class)->wherePivot('owner', true);
    }

    function notOwner(): BelongsToMany {
        return $this->belongsToMany(User::class)->wherePivot('owner', false);
    }
}
