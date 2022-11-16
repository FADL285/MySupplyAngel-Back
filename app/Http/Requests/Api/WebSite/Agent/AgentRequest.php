<?php

namespace App\Http\Requests\Api\WebSite\Agent;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class AgentRequest extends FormRequest
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
        $status = $this->agent ? 'nullable' : 'required';
        return [
            'title'          => 'required|string|min:2|max:255',
            'desc'           => 'required|string|min:2|max:500',
            'agent_type'     => 'required|in:agent,distrebutor',
            'type'           => 'required|in:required_agent_or_distrebutor,potential_agent_or_potential_distrebutor',
            'expiry_date'    => 'nullable|date|after:'.now(),
            'company_name'   => 'required',
            'product_name'   => 'required',
            'category_ids'   => 'required|array',
            'category_ids.*' => 'exists:categories,id|distinct',
            'agent_images'   => $status.'|array',
            'agent_images.*' => 'file',
            'agent_files'    => 'nullable|array',
            'agent_files.*'  => 'file',
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
