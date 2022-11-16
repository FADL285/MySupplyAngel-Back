<?php

namespace App\Http\Requests\Api\Dashboard\Notification;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class NotificationRequest extends FormRequest
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
            'all'        => 'required|in:0,1',
            'user_ids'   => 'nullable|array|required_if:all,0',
            'user_ids.*' => 'nullable|exists:users,id,user_type,client',
            'title'      => 'required|string|between:3,200',
            'body'       => 'required|string|between:3,10000',
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
