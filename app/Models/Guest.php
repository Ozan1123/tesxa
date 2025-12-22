<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Guest extends Model
{
    protected $fillable = [
        'name',
        'guest_type',
        'purpose',
        'class_info',
        'gender',
        'photo_path',
    ];
}
