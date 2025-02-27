<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingSeeder extends Seeder
{
    public function run()
    {
        Setting::setValue('promptpay_id', '0987654321');
    }
}
