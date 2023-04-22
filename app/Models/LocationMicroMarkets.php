<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LocationMicroMarkets extends Model
{
    use HasFactory;

    public $timestamps = true;
    protected $fillable = ['name', 'location_id', 'is_active', 'created_at'];
    protected $hidden = [
        'created_at',
        'updated_at',
        'is_active',
    ];
}
