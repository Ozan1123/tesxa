<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Guest extends Model
{
    protected $fillable = [
        'name',
        'type', // VIP or General
        'institution',
        'guest_type', // Categories like 'Orang Tua', 'Dinas'
        'purpose',
        'class_info',
        'gender',
        'photo_path',
        'face_descriptor',
    ];

    public function visits()
    {
        return $this->hasMany(Visit::class);
    }
}
