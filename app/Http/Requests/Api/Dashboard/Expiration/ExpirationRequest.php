<?php

namespace App\Http\Requests\Api\Dashboard\Expiration;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ExpirationRequest extends FormRequest
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
        $status = $this->expiration ? 'nullable' : 'required';

        return [
            'user_id'             => 'required|exists:users,id',
            'title'               => 'required|string|min:2|max:255',
            'desc'                => 'required|string|min:2|max:500',
            'type'                => 'required|in:liquidation,expiration',
            'expiry_date'         => 'nullable|date|after:'.now(),
            'company_name'        => 'required|min:2',
            'product_name'        => 'required|min:2',
            'category_ids'        => 'required|array',
            'category_ids.*'      => 'exists:categories,id|distinct',
            'expiration_images'   => $status .'|array',
            'expiration_images.*' => 'file',
            'expiration_files'    => 'nullable|array',
            'expiration_files.*'  => 'file',
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
