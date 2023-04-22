<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropertyMaster extends Model
{
    use HasFactory;

    public $timestamps = true;
    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $hidden = [
        'created_at',
        'updated_at',
        'is_active',
    ];

    public function details()
    {
        return $this->hasMany(PropertyDetails::class, 'property_id', 'id')
            ->select('id', 'property_id', 'field', 'value');
    }

    public function files()
    {
        return $this->hasMany(PropertyFiles::class, 'property_id', 'id')
            ->where('is_active', 1);
    }
    public function location()
    {
        return $this->hasMany(Locations::class, 'id','location')->select('id','name');
    }
    public function microMarket()
    {
        return $this->hasMany(LocationMicroMarkets::class, 'id','micromarket')->select('id','name');
    }

    public function buyorlease()
    {
        return $this->hasMany(SiteStatics::class, 'id','buyorlease')->select('id','name');
    }
    public function availability()
    {
        return $this->hasMany(SiteStatics::class, 'id','availability')->select('id','name');
    }
    public function types()
    {
        return $this->hasMany(SiteStatics::class, 'id','type')->select('id','name');
    }

}
