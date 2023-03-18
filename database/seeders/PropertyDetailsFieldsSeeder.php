<?php

namespace Database\Seeders;

use App\Models\PropertyFields;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PropertyDetailsFieldsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        PropertyFields::truncate();
        $fields = [
            "particulars",
            "park_layout",
            "building_layout",
            "total_park_area",
            "total_building_area",
            "single_building_area",
            "apron_area",
            "mezzanine",
            "office_area",
            "grid",
            "clear_span",
            "photographs",
            "total_ready_boxes",
            "total_ready_size",
            "existing_tenants",
            "bts_possibility",
            "centre_height",
            "eaves_height",
            "floor_type",
            "floor_load_capacity",
            "plinth_height",
            "number_of_docks",
            "dock_dimensions",
            "dock_levellers",
            "canopy",
            "roof_type",
            "roof_slope",
            "insulation",
            "puff_panels_glasswool",
            "ventilation",
            "acph",
            "hvac",
            "internal_lighting",
            "outside_lighting",
            "daylight",
            "parking",
            "fire_hydrant",
            "fire_sprinklers",
            "fire_extinguishers",
            "fire_doors",
            "water_source",
            "tank_capacity",
            "stp_soak-pit",
            "power_source",
            "dg_set",
            "roof_ladder",
            "solar_panels_collateral_load",
            "rain_water_harvesting",
            "ramp_for_mhe_movement",
            "weigh_bridge",
            "mlps_tower",
            "racking",
            "cranes"];

            foreach ($fields as $field) {
                PropertyFields::insert(["name"=>$field]);
            }

    }
}
