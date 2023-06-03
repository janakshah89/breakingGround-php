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
        $perPage = !empty($params['perPage']) ? $params['perPage'] : 6;
        $properties = $properties->paginate($perPage);
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
                $data = Excel::toArray(new PropertyImport(), $request->file('post_file'));
                if (!empty($data[0])) {
                    $staticArray['propetyFields'] = PropertyFields::pluck('id', 'slug')->toArray();

                    $staticArray['siteStatic'] = SiteStatics::pluck('id', 'name')->toArray();
                    $staticArray['siteStatic'] = array_change_key_case($staticArray['siteStatic'], CASE_LOWER);

                    $staticArray['location'] = Locations::pluck('id', 'name')->toArray();
                    $staticArray['location'] = array_change_key_case($staticArray['location'], CASE_LOWER);

                    $staticArray['microMarket'] = LocationMicroMarkets::pluck('id', 'name')->toArray();
                    $staticArray['microMarket'] = array_change_key_case($staticArray['microMarket'], CASE_LOWER);

                    $nA = [];
                    foreach ($data[0] as $fKey => $first) {
                        if ($fKey == 0) {
                            continue;
                        }
                        foreach ($first as $key => $value) {
                            if (strtolower($first[71]) != 'yes') {
                                if (empty($first[0])) {
                                    continue;
                                }
                                $ikey = $first[0];
                                if ($key < 19) {
                                    $nA[$ikey]['property'][$data[0][0][$key]] = $value;
                                } elseif ($key < 71) {
                                    $nA[$ikey]['details'][$data[0][0][$key]] = $value;
                                }
                            }
                            if (strtolower($first[71]) == 'yes' && !empty($first[72]) && !empty($first[74])) {
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
                    return $this->successResponse($process, "Import process finished");
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
        // echo "<pre>";
        // print_r($staticArray);
        // die();
        foreach ($data as $key => $value) {
            $record = PropertyMaster::where('name', $key)->first();
            $property = $value['property'];
            // echo "<pre>";
            // print_r($property);
            // die();
            $property['buyorlease'] = ($property['buyorlease'] == "Lease") ? 1 : 0;

            $property['availability'] = strtolower($property['availability']);
            $property['location'] = strtolower($property['location']);
            $property['micromarket'] = strtolower($property['micromarket']);
            $property['type'] = strtolower($property['type']);

            if (array_key_exists($property['type'], $staticArray['siteStatic'])) {
                $property['type'] = $staticArray['siteStatic'][$property['type']];
            } else {
                $ret[$key] = [
                    "name" => $property['name'],
                    "status" => "failed",
                    'value' => $property['type'] . " Type is not valid",
                ];
                continue;
            }
            if (array_key_exists($property['availability'], $staticArray['siteStatic'])) {
                $property['availability'] = $staticArray['siteStatic'][$property['availability']];
            } else {
                $ret[$key] = [
                    "name" => $property['name'],
                    "status" => "failed",
                    'value' => $property['availability'] . " Availablity is not valid",
                ];
                continue;
            }
            if (array_key_exists($property['location'], $staticArray['location'])) {
                $property['location'] = $staticArray['location'][$property['location']];
            } else {
                $ret[$key] = [
                    "name" => $property['name'],
                    "status" => "failed",
                    'value' => $property['location'] . " Location is not valid",
                ];
                continue;
            }
            if (array_key_exists($property['micromarket'], $staticArray['microMarket'])) {
                $property['micromarket'] = $staticArray['microMarket'][$property['micromarket']];
            } else {
                $ret[$key] = [
                    "name" => $property['name'],
                    "status" => "failed",
                    'value' => $property['micromarket'] . " Micromarket is not valid",
                ];
                continue;
            }
            $property['user_id'] = 1;
            if (empty($record->id)) {
                $create = PropertyMaster::create($property);
                $property_id = $create->id;
            } else {
                $property_id = $record->id;
                $record->update($property);
            }
            Log::info("Master Id: " . $property_id);

            $tpath = base_path() . '/public/uploads/' . $property_id . "/";
            File::ensureDirectoryExists($tpath, 0777, true, true);

            // echo "<pre>";
            // print_r($value['details']);
            // die();
            foreach ($value['details'] as $dKey => $dValue) {
                if (($dKey == 'park_layout' || $dKey == 'building_layout' || $dKey == 'photographs') && !empty($dValue)) {
                    $fpath = base_path() . '/public/uploads/parin/' . $dValue;
                    if (file_exists($fpath)) {
                        File::move($fpath, $tpath . $dValue);
                    }
                }
                $fields = [
                    'field' => $staticArray['propetyFields'][$dKey],
                    'value' => $dValue,
                    'property_id' => $property_id,
                ];
                PropertyDetails::updateOrCreate(
                    ['property_id' => $property_id, 'field' => $staticArray['propetyFields'][$dKey]], $fields);
            }

            foreach ($value['files'] as $fValue) {
                Log::info($fValue);
                if ($fValue['file_type'] == 'image' || $fValue['file_type'] == 'pdf') {
                    $fpath = base_path() . '/public/uploads/parin/' . $fValue['file'];
                    if (file_exists($fpath)) {
                        File::move($fpath, $tpath . $fValue['file']);
                        $fields = [
                            'property_id' => $property_id,
                            'file' => $fValue['file'],
                            'display_name' => $fValue['display_name'] ?? "",
                            'file_type' => $fValue['file_type'],
                        ];
                        PropertyFiles::updateOrCreate(['property_id' => $property_id, 'file' => $fValue['file']],
                            $fields);
                    }
                } else {
                    $fields = [
                        'property_id' => $property_id,
                        'file' => $fValue['file'],
                        'display_name' => $fValue['display_name'] ?? "",
                        'file_type' => $fValue['file_type'],
                    ];
                    PropertyFiles::updateOrCreate(['property_id' => $property_id, 'file' => $fValue['file']], $fields);
                }
            }
            $ret[$key] = ["name" => $key, "status" => "success", 'value' => $property_id];
        }
        return $ret;
    }
}
