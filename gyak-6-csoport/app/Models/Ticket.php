<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
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
}
