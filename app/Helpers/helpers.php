<?php

use App\Models\Setting;
use Illuminate\Support\Facades\Schema;

function setting($attr)
{
    if (Schema::hasTable('settings')) {
        $phone = $attr;

        if ($attr == 'phone') {
            $attr = 'phones';
        }

        $setting = Setting::where('key', $attr)->first() ?? [];

        if ($attr == 'logo') {
            return !empty($setting) ? asset('storage/images/setting') . "/" . $setting->value : asset('dashboardAssets/images/icons/logo_sm.png');
        }

        if (!empty($setting)) {
            return $setting->value;
        }

        return false;
    }

    return false;
}
