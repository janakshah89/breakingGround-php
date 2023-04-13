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
            ["Park Layout","park_layout","Area"],
            ["Building Layout","building_layout","Area"],
            ["Total Building Area","total_building_Area","Area"],
            ["Single Building Area","single_building_Area","Area"],
            ["Apron Area","apron_Area","Area"],
            ["Mezzanine","mezzanine","Area"],
            ["Office Area","office_Area","Area"],
            ["Grid","grid","Area"],
            ["Clear Span","clear_span","Area"],
            ["Photographs","photographs","Area"],
            ["Total Ready Boxes","total_ready_boxes","Area"],
            ["Total Ready Size","total_ready_size","Area"],
            ["Existing Tenants","existing_tenants","Area"],
            ["BTS Possibility?","bts_possibility","Area"],
            ["Centre Height","centre_height","Heights"],
            ["Eaves Height","eaves_height","Heights"],
            ["Floor Type","floor_type","Floor"],
            ["Floor Load Capacity","floor_load_capacity","Floor"],
            ["Plinth Height","plinth_height","Plinth & Docks"],
            ["Number of Docks","number_of_docks","Plinth & Docks"],
            ["Dock Dimensions (H X W)","dock_dimensions","Plinth & Docks"],
            ["Dock Levellers","dock_levellers","Plinth & Docks"],
            ["Canopy","canopy","Roof & Wall Panels / Partitions"],
            ["Canopy","roof_type","Roof & Wall Panels / Partitions"],
            ["Roof Slope","roof_slope","Roof & Wall Panels / Partitions"],
            ["Insulation","insulation","Roof & Wall Panels / Partitions"],
            ["Puff Panels / Glasswool","puff_panels_glasswool","Roof & Wall Panels / Partitions"],
            ["Ventilation (Ridge/Turbo/Louvers/HVLS Fans)","ventilation","Ventilation"],
            ["ACPH (Air Changes Per Hour)","acph","Ventilation"],
            ["HVAC","hvac","Ventilation"],
            ["Internal Lighting (Lux Level)","internal_lighting","Lighting"],
            ["Outside Lighting (Lux Level)","outside_lighting","Lighting"],
            ["Daylight","daylight","Lighting"],
            ["Parking","parking","Parking"],
            ["Fire Hydrant","fire_hydrant","Fire Protection System"],
            ["Fire Sprinklers","fire_sprinklers","Fire Protection System"],
            ["Fire Extinguishers","fire_extinguishers","Fire Protection System"],
            ["Fire Doors / Emergency Exit","fire_doors","Fire Protection System"],
            ["Water Source","water_source","Water Source, Storage & Drainage"],
            ["Tank Capacity with Jockey & Diesel Pump","tank_capacity","Water Source, Storage & Drainage"],
            ["STP/Soak-Pit/ETP","stp_soak-pit","Water Source, Storage & Drainage"],
            ["Power Source","power_source","Power"],
            ["DG Set","dg_set","Power"],
            ["Roof Ladder","roof_ladder","Maintenance"],
            ["Solar Panels Collateral Load","solar_panels_collateral_load","Sustainibility"],
            ["Rain Water Harvesting","rain_water_harvesting","Sustainibility"],
            ["Ramp for MHE movement","ramp_for_mhe_movement","Utilities"],
            ["Weigh Bridge","weigh_bridge","Utilities"],
            ["MLPS Tower","mlps_tower","Utilities"],
            ["Racking","racking","Utilities"],
            ["Cranes","cranes","Utilities"]
        ];

            foreach ($fields as $field) {
                PropertyFields::insert(["slug"=>$field[1],"name"=>$field[0],"category"=>$field[2]]);
            }

    }
}
