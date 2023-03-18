<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientTestimonials extends Model
{
    use HasFactory;
    public $timestamps = true;
    protected $fillable = ['name','comment','file','stars','orders','is_active','created_at'];
}
