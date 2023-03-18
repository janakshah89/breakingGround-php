<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteStatics extends Model
{
    use HasFactory;
    public $timestamps = true;
    protected $fillable = ['name','slug','description','sort_order','created_at'];
}
