<?php

namespace App\Http\Controllers;

use App\Models\PropertyDetails;
use App\Models\PropertyMaster;
use Faker\Factory;
use Faker\Provider\Company;
use Illuminate\Http\Request;

class PropertyMasterController extends Controller
{
    public function list()
    {
        $properties = PropertyMaster::with('location','buyorlease','types','availability','microMarket')
            ->where('is_active',1)->paginate(2);
        return $this->successResponse($properties,trans('auth.getRecords'));
    }
    public function create()
    {

        $faker = Factory::create();
        $originalPrice = $faker->randomFloat('2', 1200, 30000);
        $discountMin = $originalPrice / 2;
        $discountMax = $originalPrice - 5;

        $dataArray = new PropertyMaster();
        $dataArray->name = $faker->name();
        $dataArray->user_id = 1;
        $dataArray->buyorlease = $faker->randomElement([0,1]);
        $dataArray->type = $faker->randomElement([1,2,3]);
        $dataArray->availability = $faker->randomElement([4,5]);
        $dataArray->location = $faker->randomElement([1,2]);
        $dataArray->micromarket = $faker->randomElement([1,2,3,4,5,6,7]);
        $dataArray->description = $faker->name();
        $dataArray->sqft = $faker->randomElement([1000,15000,25000,35000,50000]);
        $dataArray->rate = $originalPrice;
        $dataArray->discount = $faker->numberBetween($discountMin, $discountMax);
        $dataArray->price = $originalPrice;
        $dataArray->address = $faker->streetAddress();
        $dataArray->address1 = $faker->streetAddress();
        $dataArray->city = $faker->city();
        $dataArray->state = 'Gujarat';
        $dataArray->pincode = $faker->postcode();
        $dataArray->lat = $faker->randomFloat(6, 1, 100);
        $dataArray->lan = $faker->randomFloat(6, 1, 100);
        $dataArray->is_premium = 0;
        $dataArray->is_active = 1;
        $dataArray->created_at = date('Y-m-d H:i:s');
        $dataArray->save();

        $propertyDetails = PropertyDetails::where('property_id', 1)->get();
        $propertyDetails->map(function($i) use ($dataArray) {
            unset($i['id']);
            $i->property_id = $dataArray->id;
            return $i;
        });
        PropertyDetails::insert($propertyDetails->toArray());
        return $this->successResponse($dataArray,trans('auth.insertRecords'));
    }
}
