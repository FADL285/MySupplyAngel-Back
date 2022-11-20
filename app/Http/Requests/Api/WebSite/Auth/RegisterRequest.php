<?php

namespace App\Http\Requests\Api\WebSite\Auth;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class RegisterRequest extends FormRequest
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
            'name'                    => 'required|string|between:3,250',
            'password'                => 'required|min:6|confirmed',
            'email'                   => 'required|email|unique:users,email,NULL,id,deleted_at,NULL',
            'phone_code'              => 'required|numeric|exists:countries,phone_code',
            'phone'                   => 'required|numeric|digits_between:9,23|unique:users,phone', //NULL,id,phone_code,'.$this->phone_code,
            'whats'                   => 'nullable|numeric|digits_between:9,23|unique:users,whats', //NULL,id,phone_code,'.$this->phone_code,
            'avatar'                  => 'nullable|image|mimes:jpg,jpeg,png',
            'gender'                  => 'nullable|in:male,female',
            'country_id'              => 'nullable|required_with:city_id|exists:countries,id',
            'city_id'                 => 'nullable|exists:cities,id',
            'address'                 => 'nullable|string|between:3,250',
            'company_name'            => 'nullable|string|between:3,250',
            'company_address'         => 'nullable|string|between:3,250',
            'commercial_register_num' => 'nullable|string|between:3,250',
            'tax_card_num'            => 'nullable|string|between:3,250',
            'categories'              => 'required|array',
            'categories.*'            => 'exists:categories,id',
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
