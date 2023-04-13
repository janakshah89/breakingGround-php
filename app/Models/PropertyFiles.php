<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropertyFiles extends Model
{
    use HasFactory;
    use HasFactory;
    public $timestamps = true;
    protected $guarded = ['id', 'created_at', 'updated_at'];
}
