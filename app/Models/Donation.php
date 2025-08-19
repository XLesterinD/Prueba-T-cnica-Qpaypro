<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Donation extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'last_name',
        'email',
        'phone',
        'amount',
        'message',
        'city',
        'country',
        'state',
        'address',
        'zip_code',
        'nit',
        'status',
    ];
}
