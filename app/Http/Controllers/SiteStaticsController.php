<?php

namespace App\Http\Controllers;

use App\Models\ClientTestimonials;
use App\Models\SiteStatics;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SiteStaticsController extends Controller
{
    public function listItems(Request $request): JsonResponse
    {
        try{
            $records =  SiteStatics::get();
            return $this->successResponse($records,trans('auth.getRecords'));
        }catch(ModelNotFoundException $e){
            return $this->sendBadRequest($e->getMessage());
        }
    }
    public function getItems(Request $request, $slug): JsonResponse
    {
        try{
            $records =  SiteStatics::whereSlug($slug)->get();
            if(!$records->count()){
                return $this->recordNotFound();
            }
            return $this->successResponse($records,trans('auth.getRecords'));
        }catch(ModelNotFoundException $e){
            return $this->sendBadRequest($e->getMessage());
        }
    }
}
