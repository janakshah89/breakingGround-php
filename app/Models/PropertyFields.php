<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropertyFields extends Model
{
    use HasFactory;
    public $timestamps = true;
    protected $fillable = ['name','is_active','created_at'];
}
