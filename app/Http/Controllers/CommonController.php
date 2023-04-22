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

class CommonController extends Controller
{
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
            OauthAccessToken::where('user_id', Auth::user()->id)->delete();
            $this->currentUser = Auth::user();
            // check account is active or deactivate
            if ($this->currentUser['status'] != 1) {
                Session::flush();
                $accLocked = trans('message.accountLocked');
                return $this->sendAccessDenied($accLocked);
            }
            $this->currentUser->token = $this->currentUser->createToken('WWL')->accessToken;
            $this->currentUser->isLogin = true;
            return $this->successResponse($this->currentUser, trans('message.loginSucc'));
        } else {
            return $this->sendBadRequest(trans('message.loginErr'));
        }
    }
}
