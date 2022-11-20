<?php

namespace App\Http\Requests\Api\Dashboard\Profile;

use Exception;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
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
        $country_id = $this->country_id ?? optional(auth('api')->user()->country)->id;
        return [
            'name'       => 'nullable|string',
            'email'      => ['nullable', Rule::unique('users')->ignore(auth('api')->id())->where(fn ($query) => $query->whereNull('deleted_at'))],
            'avatar'     => 'nullable|file',
            'phone_code' => 'nullable|integer',
            'phone'      => 'nullable|integer',
            'country_id' => 'nullable|exists:countries,id',
            'city_id'    => 'nullable|exists:countries,id',
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
