<?php

namespace App\Http\Controllers\Api\Dashboard\Setting;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Dashboard\Setting\SettingRequest;
use App\Http\Resources\Api\Dashboard\Setting\SettingResource;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        return (new SettingResource(null))->additional(['status' => 'success', 'message' => '']);
    }

    public function store(SettingRequest $request)
    {
        foreach ($request->validated() as $key => $value) {
            Setting::updateOrCreate(['key' => trim($key)], ['value' => $value]);
        }

        return (new SettingResource(null))->additional(['status' => 'success', 'message' => trans('dashboard.update.success')]);
    }
}
