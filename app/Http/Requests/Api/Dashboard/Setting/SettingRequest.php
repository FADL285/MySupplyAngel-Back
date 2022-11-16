<?php

namespace App\Http\Requests\Api\Dashboard\Setting;

use Illuminate\Foundation\Http\FormRequest;

class SettingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'email'                => "nullable|email",
            'use_sms_service'      => "nullable|in:enable,disable",
            'sms_provider'         => "nullable|required_if:use_sms_service,enable|in:hisms,net_powers,sms_gateway",
            'sms_username'         => "nullable|required_if:use_sms_service,enable|string|between:3,250",
            'sms_password'         => "nullable|required_with:sms_username",
            'sms_sender_name'      => "nullable|string|between:3,250",
            'project_name'         => "nullable",
            'facebook'             => "nullable|url",
            'twitter'              => "nullable|url",
            'youtube'              => "nullable|url",
            'instagram'            => "nullable|url",
            'whatsapp'             => "nullable|string|max:250",
            'sms_message'          => "nullable|string|max:250",
            'address'              => "nullable|string|max:250",
            'messenger'            => "nullable|string",
            'linkedin'             => "nullable|string",
            'about_ar'             => "nullable|string",
            'about_en'             => "nullable|string",
            'privacy_en'           => "nullable|string",
            'privacy_ar'           => "nullable|string",
            'terms_en'             => "nullable|string",
            'terms_ar'             => "nullable|string",
            'why_us_ar'            => "nullable|string",
            'why_us_en'            => "nullable|string",
            'description_location' => "nullable|string",
            'phone'                => "nullable|string|max:255",
        ];
    }
}
