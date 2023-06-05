<?php

namespace App\Http\Controllers;

use App\Models\Contactus;
use App\Models\Locations;
use App\Models\LocationMicroMarkets;
use App\Models\PropertyRequest;
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
            // check account is active
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

    public function contactus(Request $request)
    {
        $input = $this->request->post();
        $rules = Contactus::$rules;
        $validator = Validator::make($input, $rules);

        if ($validator->fails()) {
            return $this->sendBadRequest($validator->errors());
        }

        $template = 'email.contact-us';
        $to = env('DEVELOPER_EMAIL');

        $emailData = ['name' => $input['name'], 'message' => $input['message'], 'data' => $input];
        sendEmail($template, $to, "A new contact request by Customer", $emailData);
        Contactus::create($input);
        return $this->successResponse($input, "Thank you for the interest, we will reach out to you shorty.");
    }

    public function property_request(Request $request)
    {
        $input = $request->all();;
        $rules = PropertyRequest::$rules;

        $validator = Validator::make($input, $rules);
        if ($validator->fails()) {
            return $this->sendBadRequest($validator->errors());
        }
        $property = new PropertyRequest();
        $attachedFiles = [];
        if ($request->hasFile('file')) {
            $property->file = $request->file('file')->getClientOriginalName();
            $request->file('file')->move(base_path() . '/public/uploads/PropertyRequest', $property->file);
            $attachedFiles[] = base_path() . '/public/uploads/PropertyRequest/' . $property->file;
        }
        $property->name = $request['name'];
        $property->company = $request['company'];
        $property->phone = $request['phone'];
        $property->email = $request['email'];

        $template = 'email.property-request';
        $to = env('DEVELOPER_EMAIL');
        $emailData = ['name' => $input['name'], 'data' => $input];
        $tt = sendEmail($template, $to, "A new Property request received", $emailData, $attachedFiles);
        return $this->successResponse($tt, "Thank you for the interest, we will reach out to you shorty.");
    }
}
