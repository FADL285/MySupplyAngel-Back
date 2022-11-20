<?php

namespace App\Http\Requests\Api\Dashboard\Admin;

use App\Models\User;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class AdminRequest extends FormRequest
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
        $admin = isset($this->admin) ? User::where('user_type', 'admin')->findOrFail($this->admin) : null;

        $status = isset($this->admin) ? 'nullable' : 'required';
        $country_id = $this->country_id ?? optional($admin->country)->id;

        return [
            'name'          => $status . '|string|max:45',
            'avatar'        => $status . '|file',
            'phone_code'    => $status . '|exists:countries,phone_code',
            'phone'         => [$status, Rule::unique('users')->ignore($admin)->where(fn ($query) => $query->whereNull('deleted_at'))],
            'email'         => [$status, Rule::unique('users')->ignore($admin)->where(fn ($query) => $query->whereNull('deleted_at'))],
            'gender'        => $status . '|in:female,male,else',
            'is_active'     => $status . '|in:0,1',
            'is_ban'        => $status . '|in:0,1',
            'ban_reason'    => 'nullable|in:0,1',
            'country_id'    => $status . '|exists:countries,id',
            'city_id'       => $status . '|exists:cities,id,country_id,' . $country_id,
            'password'      => $status . '|min:6|confirmed'
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
