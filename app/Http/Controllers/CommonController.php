<?php

namespace App\Http\Controllers;

use App\Models\Locations;
use App\Models\LocationMicroMarkets;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use App\Models\OauthAccessToken;
use Illuminate\Support\Facades\Hash;

class CommonController extends Controller
{
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function listLocation(Request $request): JsonResponse
    {
        try {
            $records = Locations::orderby('name')->get();
            return $this->successResponse($records, trans('auth.getRecords'));
        } catch (ModelNotFoundException $e) {
            return $this->sendBadRequest($e->getMessage());
        }
    }

    public function listLocationMicroMarket(Request $request, $location = null)  //: JsonResponse
    {
        try {
            $records = new LocationMicroMarkets();
            $params = $request->all();
            if (!empty($location)) {
                $records = $records->where("location_id", $location);
            }

            if (!empty($params['name'])) {
                $records = $records->where("name", "like", '%' . trim($params['name']) . '%');
            }
            $records = $records->orderby('name')->whereIs_active('1')->get();
            return $this->successResponse($records, trans('auth.getRecords'));
        } catch (ModelNotFoundException $e) {
            return $this->sendBadRequest($e->getMessage());
        }
    }

    public function login(Request $request)
    {
        $input = $this->request->post();
        $rememberMe = false;
        if (Auth::attempt($input, $rememberMe)) {
            $this->currentUser = Auth::user();
            // check account is active or deactivate
            if ($this->currentUser['is_active'] != 1) {
                Session::flush();
                return $this->sendAccessDenied("Sorry the User is not active");
            }
            $token = $this->currentUser->createToken('PIVOT', ['*']);
            return $this->successResponse(['token' => $token->plainTextToken], "Login Success");
        } else {
            return $this->sendBadRequest("Email or Password in wrong");
        }
    }
}
