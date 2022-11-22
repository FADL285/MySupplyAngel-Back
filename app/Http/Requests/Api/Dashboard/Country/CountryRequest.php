<?php

namespace App\Http\Requests\Api\Dashboard\Country;

use App\Models\Country;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class CountryRequest extends FormRequest
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
        $status = $this->country ? 'nullable' : 'required';
        $country = isset($this->country) ? Country::findOrFail($this->country) : null;

        $rules = [
            'phone_code' => [$status, 'numeric', 'digits_between:1,3', Rule::unique('countries')->ignore($country)->where(fn ($query) => $query->whereNull('deleted_at'))],
            'short_name' => 'nullable|string',
            'continent'  => $status.'|in:africa,europe,asia,south_america,north_america,australia',
            'image'      => $status.'|file',
        ];

        foreach(config('translatable.locales') as $locale)
        {
            $rules[$locale.'.name']        = $status.'|string|between:2,45';
            $rules[$locale.'.nationality'] = $status.'|string|between:2,45';
            $rules[$locale.'.currency']    = $status.'|string|between:2,45';
            $rules[$locale.'.slug']        = 'nullable|string|between:2,45';
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
