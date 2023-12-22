<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Setting;

class HoldingOptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // PREFIXES
        $prefixValues = ['Important', 'F', 'R', 'R/F', 'T'];
        $prefixSetting = Setting::where('field', 'prefix')->first();

        foreach ($prefixValues as $value) {
            $prefixSetting->holdingOptions()->firstOrCreate([
                'value' => $value
            ]);
        }

        // PATRON GROUPS
        $groupValues = ['Group 1', 'Group 2', 'Group 3', 'Group 4', 'Group 5'];
        $groupSetting = Setting::where('field', 'group')->first();

        foreach ($groupValues as $value) {
            $groupSetting->holdingOptions()->firstOrCreate([
                'value' => $value
            ]);
        }

        // FUND
        $fundValues = ['purchased', 'donated'];
        $fundSetting = Setting::where('field', 'fund')->first();

        foreach ($fundValues as $value) {
            $fundSetting->holdingOptions()->firstOrCreate([
                'value' => $value
            ]);
        }

        // VENDOR
        $vendorValues = ['vendor1', 'vendor2', 'vendor3'];
        $vendorSetting = Setting::where('field', 'vendor')->first();

        foreach ($vendorValues as $value) {
            $vendorSetting->holdingOptions()->firstOrCreate([
                'value' => $value
            ]);
        }

        // FORMAT
        $formatValues = ['Book', 'Periodical', 'OER', 'CD-ROM', 'Thesis'];
        $formatSetting = Setting::where('field', 'format')->first();

        foreach ($formatValues as $value) {
            $formatSetting->holdingOptions()->firstOrCreate([
                'value' => $value
            ]);
        }
    }
}
