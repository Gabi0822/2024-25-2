<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Comment extends Model
{
    /** @use HasFactory<\Database\Factories\CommentFactory> */
    use HasFactory;

    protected $fillable = [
        'text',
        'filename',
        'filename_hash',
        'ticket_id',
        'user_id'
    ];

    public function ticket(): BelongsTo {
        return $this->belongsTo(Ticket::class);
    }

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }
}
