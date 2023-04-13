<?php

namespace App\Http\Controllers;

use App\Models\Locations;
use App\Models\LocationMicroMarkets;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CommonController extends Controller
{
    public function listLocation(Request $request): JsonResponse
    {
        try{
            $records =  Locations::get();
            return $this->successResponse($records,trans('auth.getRecords'));
        }catch(ModelNotFoundException $e){
            return $this->sendBadRequest($e->getMessage());
        }
    }

    public function listLocationMicroMarket(Request $request): JsonResponse
    {
        try{
            $records = new LocationMicroMarkets();
            if(!empty($request)){
                //$records = $records->where("location_id",$request);
            }
            $records = $records->get();
            return $this->successResponse($records,trans('auth.getRecords'));
        }catch(ModelNotFoundException $e){
            return $this->sendBadRequest($e->getMessage());
        }
    }
}
