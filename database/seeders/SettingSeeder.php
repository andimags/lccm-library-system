<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $fields = ['location', 'format', 'vendor', 'fund', 'prefix', 'group', 'cutter'];

        foreach($fields as $field){
            Setting::create([
                'field' => $field
            ]);
        }

        Setting::create([
            'field' => 'enable_automatic_fines',
            'value' => 'yes'
        ]);
    }
}
