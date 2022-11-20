<?php

namespace App\Http\Requests\Api\WebSite\Tender;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class TenderRequest extends FormRequest
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
        $status = $this->tender ? 'nullable' : 'required';
        return [
            'title'                       => 'required|string|min:2|max:255',
            'desc'                        => 'required|string|min:2|max:500',
            'insurance_value'             => 'required|numeric|min:0',
            'expiry_date'                 => 'required|date|after:'.now(),
            'company_name'                => 'required|string|min:2',
            'category_ids'                => 'required|array',
            'category_ids.*'              => 'exists:categories,id|distinct',
            'tender_specifications_value' => 'required|numeric|min:0',
            'tender_specifications_file'  => $status.'|file',
            'tender_images'               => $status.'|array',
            'tender_images.*'             => 'file',
            'tender_other_files'          => 'nullable|array',
            'tender_other_files.*'        => 'file',
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
