<?php

namespace App\Http\Controllers;

use App\Imports\PropertyImport;
use App\Models\LocationMicroMarkets;
use App\Models\Locations;
use App\Models\PropertyDetails;
use App\Models\PropertyFields;
use App\Models\PropertyFiles;
use App\Models\PropertyMaster;
use App\Models\SiteStatics;
use Faker\Factory;
use Faker\Provider\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Console\Input\Input;
use Maatwebsite\Excel\Excel as ExcelExcel;
use Maatwebsite\Excel\Facades\Excel;
use File;

class PropertyMasterController extends Controller
{
    public function list(Request $request)
    {
        $params = $request->all();
        $properties = PropertyMaster::with('location', 'types', 'availability', 'microMarket');

        if (isset($params['borl'])) {
            $properties = $properties->where("buyorlease", $params['borl']);
        }
        if (!empty($params['type'])) {
            $properties = $properties->where("type", $params['type']);
        }
        if (!empty($params['availability'])) {
            $properties = $properties->where("availability", $params['availability']);
        }
        if (!empty($params['location'])) {
            $properties = $properties->where("location", $params['location']);
        }
        if (!empty($params['micromarket'])) {
            $properties = $properties->where("micromarket", $params['micromarket']);
        }
        if (!empty($params['sizeMax']) && !empty($params['sizeMin'])) {
            $properties = $properties->whereBetween("sqft", [$params['sizeMin'], $params['sizeMax']]);
        }
        $properties = $properties->where('is_active', 1);

        if (!$properties->count()) {
            return $this->recordNotFound();
        }
        $properties = $properties->paginate(6);
        return $this->successResponse($properties, trans('auth.getRecords'));
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
        $dataArray->buyorlease = $faker->randomElement([0, 1]);
        $dataArray->type = $faker->randomElement([1, 2, 3]);
        $dataArray->availability = $faker->randomElement([4, 5]);
        $dataArray->location = $faker->randomElement([1, 2]);
        $dataArray->micromarket = $faker->randomElement([1, 2, 3, 4, 5, 6, 7]);
        $dataArray->description = $faker->text();
        $dataArray->sqft = $faker->randomElement([1000, 15000, 25000, 35000, 50000]);
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
        $propertyDetails->map(function ($i) use ($dataArray) {
            unset($i['id']);
            $i->property_id = $dataArray->id;
            return $i;
        });
        PropertyDetails::insert($propertyDetails->toArray());
        return $this->successResponse($dataArray, trans('auth.insertRecords'));
    }

    public function show(Request $request, $id)
    {
        return PropertyMaster::with('details.fields', 'location', 'types', 'availability',
            'microMarket')->whereId($id)->firstorfail();
    }

    public function upload(Request $request)
    {
        try {
            $parameters = $request->all();
            if ($request->hasFile('post_file')) {
                $extension = $request->file('post_file')->getClientOriginalExtension();
                if (!in_array($extension, array('xls', 'xlsx', 'csv'))) {
                    return $this->sendBadRequest("not a valid extension");
                }
                $path = $request->file('post_file')->getRealPath();
                $data = Excel::toArray(new PropertyImport(), $path);
                return $data;
                if (!empty($data[0])) {
                    $staticArray['propetyFields'] = PropertyFields::pluck('id', 'slug')->toArray();
                    $staticArray['siteStatic'] = SiteStatics::pluck('id', 'name')->toArray();
                    $staticArray['location'] = Locations::pluck('id', 'name')->toArray();
                    $staticArray['microMarket'] = LocationMicroMarkets::pluck('id', 'name')->toArray();
                    $nA = [];
                    foreach ($data[0] as $fKey => $first) {
                        if ($fKey == 0) {
                            continue;
                        }
                        foreach ($first as $key => $value) {
                            if (strtolower($first[71]) != 'yes') {
                                $ikey = $first[0];
                                if ($key < 19) {
                                    $nA[$ikey]['property'][$data[0][0][$key]] = $value;
                                } elseif ($key < 71) {
                                    $nA[$ikey]['details'][$data[0][0][$key]] = $value;
                                }
                            }
                            if (strtolower($first[71]) == 'yes') {
                                $nA[$ikey]['files'][$fKey][$data[0][0][72]] = $first[72];
                                $nA[$ikey]['files'][$fKey][$data[0][0][73]] = $first[73];
                                $nA[$ikey]['files'][$fKey][$data[0][0][74]] = $first[74];
                            }
                        }
                    }
                    //return $nA;
                    // ini_set('max_execution_time', 0);
                    // set_time_limit(0);
                    // ini_set('memory_limit', '-1');
                    // ignore_user_abort(true);
                    $result = ["name" => $ikey, "status" => "failed"];
                    $process = $this->import_property_csv($nA, $staticArray);
                    return $this->successResponse($process, "Import process done");
                }
                return $this->sendBadRequest("invalid Data");
            }
            return $this->sendBadRequest("invalid File");
        } catch (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $ex) {
            return $this->notFoundRequest();
        } catch
        (\Exception $ex) {
            return $this->sendErrorResponse($ex);
        }
    }

    public function import_property_csv($data, $staticArray)
    {
        $ret = [];
        foreach ($data as $key => $value) {
            $record = PropertyMaster::where('name', $key)->first();
            $property = $value['property'];
            //return $property['type'];
            if (array_key_exists($property['type'], $staticArray['siteStatic'])) {
                $property['type'] = $staticArray['siteStatic'][$property['type']];
            } else {
                $ret[$key] = ["name" => $property['name'], "status" => "failed", 'value' => "Type is not valid"];
                continue;
            }
            if (array_key_exists($property['availability'], $staticArray['siteStatic'])) {
                $property['availability'] = $staticArray['siteStatic'][$property['availability']];
            } else {
                $ret[$key] = ["name" => $property['name'], "status" => "failed", 'value' => "Availablity is not valid"];
                continue;
            }
            if (array_key_exists($property['location'], $staticArray['location'])) {
                $property['location'] = $staticArray['location'][$property['location']];
            } else {
                $ret[$key] = ["name" => $property['name'], "status" => "failed", 'value' => "Location is not valid"];
                continue;
            }
            if (array_key_exists($property['micromarket'], $staticArray['microMarket'])) {
                $property['micromarket'] = $staticArray['microMarket'][$property['micromarket']];
            } else {
                $ret[$key] = ["name" => $property['name'], "status" => "failed", 'value' => "Micromarket is not valid"];
                continue;
            }
            $property['user_id'] = 1;
            $property_id = PropertyMaster::updateOrCreate(['id' => $record->id], $property)->id;
            // echo "<pre>";
            // print_r($value);
            // die();
            foreach ($value['details'] as $dKey => $dValue) {
                $fields = [
                    'field' => $staticArray['propetyFields'][$dKey],
                    'value' => $dValue,
                    'name' => $dKey,
                    'property_id' => $property_id,
                ];
                PropertyDetails::updateOrCreate(
                    ['property_id' => $property_id, 'field' => $staticArray['propetyFields'][$dKey]], $fields);
            }
            foreach ($value['files'] as $dKey => $dValue) {
                if ($dValue['file_type'] == 'image' || $dValue['file_type'] == 'pdf') {
                    $fpath = base_path() . '/public/uploads/parin/' . $dValue['file'];
                    $tpath = base_path() . '/public/uploads/' . $property_id . "/";
                    Log::info("FROM : " . $fpath);
                    File::ensureDirectoryExists($tpath, 777);
                    if (file_exists($fpath)) {
                        Log::info("TO : " . $tpath);
                        File::move($fpath, $tpath . $dValue['file']);
                        $fields = [
                            'property_id' => $property_id,
                            'file' => $dValue['file'],
                            'display_name' => $dValue['display_name'] ?? "",
                            'file_type' => $dValue['file_type'],
                        ];
                        PropertyFiles::insert($fields);
                    }
                } else {
                    $fields = [
                        'property_id' => $property_id,
                        'file' => $dValue['file'],
                        'display_name' => $dValue['display_name'] ?? "",
                        'file_type' => $dValue['file_type'],
                    ];
                    PropertyFiles::updateOrCreate(['property_id' => $property_id, 'file' => $dValue['file']], $fields);
                }
            }

            $ret[$key] = ["name" => 'name', "status" => "success", 'value' => $property_id];
        }
        return $ret;
    }
}
