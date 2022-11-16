<?php

namespace App\Http\Requests\Api\Dashboard\City;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class CityRequest extends FormRequest
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
        $status = isset($this->city) ? 'nullable' : 'required';

        $rules = [
            'country_id'  => $status.'|exists:countries,id',
            'short_name'  => 'nullable|string',
            'postal_code' => 'nullable|numeric|min:4',
        ];

        foreach(config('translatable.locales') as $locale)
        {
            $rules[$locale.'.name'] = $status.'|string|between:2,45';
            $rules[$locale.'.slug'] = 'nullable|string|between:2,45';
        }

        return $rules;
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
