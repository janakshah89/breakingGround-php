<?php

namespace App\Http\Controllers;

use App\Models\ClientTestimonials;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class ClientTestimonialsController extends Controller
{
    public function listItems(Request $request): JsonResponse
    {
        try{
            $records =  ClientTestimonials::get();
            foreach ($records as $value){
                $value->file = base_path()."/uploads/" . $value->file;
            }
            return $this->successResponse($records,trans('auth.getRecords'));
        }catch(ModelNotFoundException $e){
            return $this->sendBadRequest($e->getMessage());
        }
    }
}
