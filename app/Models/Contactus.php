<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contactus extends Model
{
    use HasFactory;
    protected $table = 'contact_request';
    public $timestamps = true;
    protected $fillable = ['name','company','email','phone','subject','message'];

    public static $rules =
       [
            "name"=>"required|max:190",
            "company"=>"required|max:190",
            "email"=>"required|email|max:190",
            "phone"=>"required|max:190",
            "subject"=>"required|max:190",
            "message"=>"required",
        ];

}
