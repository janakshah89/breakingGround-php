<?php

namespace Database\Seeders;

use App\Models\SiteStatics;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SiteStaticsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        SiteStatics::truncate();
        $data = [
            [
                "name"=>"Industrial",
                "slug"=>"property_type",
                "description"=>"",
                "sort_order"=>"",
            ],
            [
                "name"=>"Warehouse",
                "slug"=>"property_type",
                "description"=>"",
                "sort_order"=>"",
            ],
            [
                "name"=>"Land",
                "slug"=>"property_type",
                "description"=>"",
                "sort_order"=>"",
            ],
            [
                "name"=>"Read/BrownField",
                "slug"=>"availability",
                "description"=>"",
                "sort_order"=>"",
            ],
            [
                "name"=>"BTS/GreenField",
                "slug"=>"availability",
                "description"=>"",
                "sort_order"=>"",
            ]
        ];
        foreach ($data as $value){
            SiteStatics::insert($value);
        }
    }
}
