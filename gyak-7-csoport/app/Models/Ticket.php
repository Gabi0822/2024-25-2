<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;


class Ticket extends Model
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'title',
        'done',
        'priority',
    ];


}
