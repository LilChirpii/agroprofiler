<?php

namespace App\Imports;

use App\Models\CropDamage;
use App\Models\Farmer;
use App\Models\Farm;
use App\Models\Commodity;
use App\Models\Barangay;
use App\Models\CropDamageCause;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CropDamageImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // Look up Farmer by first and last name
        $farmer = Farmer::whereRaw("CONCAT(firstname, ' ', lastname) = ?", [$row['farmer_name']])->first();
        if (!$farmer) return null; // Skip if farmer not found

        // Look up Farm by name
        $farm = Farm::where('name', $row['farm_name'])->first();
        if (!$farm) return null; // Skip if farm not found

        // Look up Commodity
        $commodity = Commodity::where('name', $row['commodity'])->first();
        if (!$commodity) return null; // Skip if commodity not found

        // Look up Barangay
        $barangay = Barangay::where('name', $row['brgy_name'])->first();
        if (!$barangay) return null; // Skip if barangay not found

        // Look up Crop Damage Cause
        $damageCause = CropDamageCause::where('name', $row['crop_damage_cause'])->first();
        if (!$damageCause) return null; // Skip if crop damage cause not found

        return new CropDamage([
            'farmer_id' => $farmer->id,
            'farm_id' => $farm->id,
            'commodity_id' => $commodity->id,
            'brgy_id' => $barangay->id,
            'crop_damage_cause_id' => $damageCause->id,
            'total_damaged_area' => $row['total_damaged_area'],
            'partially_damaged_area' => $row['partially_damaged_area'],
            'area_affected' => $row['area_affected'],
            'severity' => $row['severity'],
            'remarks' => $row['remarks'] ?? null,
            'proof' => $row['proof'] ?? null, // Store filename, assuming it's uploaded separately
        ]);
    }
}
