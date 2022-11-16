<?php

namespace App\Http\Requests\Api\WebSite\Contact;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ContactRequest extends FormRequest
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
            'first_name' => 'nullable|min:2|max:255',
            'last_name'  => 'nullable|min:2|max:255',
            'email'      => 'required|email',
            'content'    => 'required|string|between:2,10000',
            'phone'      => 'required|min:5|max:15',
        ];
    }

    public function getValidatorInstance()
    {
        $data = $this->all();

        if (auth('api')->check())
        {
            $name = explode(" ", auth('api')->user()->name);
            $data['user_id']    = auth('api')->id();
            $data['first_name'] = $data['first_name'] ?? reset($name);
            $data['last_name']  = $data['last_name'] ?? end($name);
            $data['phone']      = $data['phone'] ?? auth('api')->user()->phone;
            $data['email']      = $data['email'] ?? (auth('api')->user()->email ?? '');
        }

        $this->getInputSource()->replace($data);
        return parent::getValidatorInstance();
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
