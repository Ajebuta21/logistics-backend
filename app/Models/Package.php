<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    use HasFactory;

    protected $fillable = [
        'senders_name',
        'senders_email',
        'senders_number',
        'recievers_name',
        'recievers_email',
        'recievers_number',
        'origin',
        'destination',
        'distance',
        'time_taken',
        'weight',
        'description',
        'tracking_id',
        'status',
        'price',
    ];
}
