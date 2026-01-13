<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Visit extends Model
{
    protected $fillable = [
        'guest_id',
        'purpose',
        'check_in_at',
        'check_out_at',
        'status',
    ];

    public function guest()
    {
        return $this->belongsTo(Guest::class);
    }
}
