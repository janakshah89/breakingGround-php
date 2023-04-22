<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropertyDetails extends Model
{
    use HasFactory;
    public $timestamps = true;
    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function fields()
    {
        return $this->hasOne(PropertyFields::class, 'id', 'field')->select('id', 'name');
    }
}

