<?php

namespace App\Http\Requests\Api\WebSite\Job;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class JobRequest extends FormRequest
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
            "country_id"   => "required|exists:countries,id",
            "city_id"      => "required|exists:cities,id",
            "expiry_date"  => "nullable|date|after:".now(),
            "job_title"    => "required|string|min:3|max:255",
            "company_name" => "required|string|min:3|max:255",
            "desc"         => "required|string|min:3|max:300",
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status'  => false,
            'data'    => null,
            'message' => $validator->messages()->first(),
        ], 422));
    }
}
