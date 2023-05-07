<?php

namespace App\Imports;

use App\Models\PropertyMaster;
use Maatwebsite\Excel\Concerns\ToModel;

class PropertyImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new PropertyMaster([
            "first_name" => $row[0],
            "last_name" => $row[1],
            "email" => $row[2],
            "mobile_number" => $row[3],
            "role_id" => 2, // User Type User
            "status" => 1,
        ]);
    }
}
