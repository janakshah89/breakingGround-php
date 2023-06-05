<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropertyRequest extends Model
{
    use HasFactory;
    protected $table = 'find_property_request';
    public $timestamps = true;
    protected $fillable = ['name','file','email','phone','company'];

    public static $rules =
        [
            "name" => "required|max:200",
            "company" => "required|max:200",
            "email" => "required|email|max:200",
            "phone" => "required|max:20",
            "file" => "mimes:jpeg,jpg,png,pdf,xlx,xlsx,csv,bmp,svg,webp,gif,pjpeg,txt",
        ];
}
