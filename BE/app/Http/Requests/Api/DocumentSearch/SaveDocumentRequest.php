<?php

namespace App\Http\Requests\Api\DocumentSearch;

use App\Http\Requests\Api\ApiBaseRequest;

class SaveDocumentRequest extends ApiBaseRequest
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
     * @return array
     */
    public function rules()
    {
        $rules = [];
        $rules['document_id'] = ['nullable', 'max:20'];
        $rules['document_name'] = ['required', 'max:50'];
        $rules['document_contents'] = ['nullable', 'max:300'];
        $rules['document_version'] = ['required', 'max:20'];
        $rules['parent_document_id'] = ['nullable', 'max:20'];
        $rules['start_date'] = ['required', 'date'];
        $rules['end_date'] = ['required', 'date'];
        $rules['available_org_cd'] = ['nullable', 'max:20'];
        $rules['product_cd'] = ['required', 'max:10'];
        $rules['disease'] = ['nullable', 'max:50'];
        $rules['medical_subjects_group_cd'] = ['nullable', 'max:5'];
        $rules['document_category_cd'] = ['nullable', 'max:3'];
        $rules['safety_information_flag'] = ['required', 'max:1'];
        $rules['off_label_information_flag'] = ['required', 'max:1'];
        $rules['file_type'] = ['required', 'max:3'];
        $rules['file_id'] = ['nullable', 'max:20'];
        if (empty($this->document_id)) {
            $rules['document_file'] = ['required', 'mimes:pdf,mp4,m4v', 'max:102400'];
        } else {
            $rules['document_file'] = ['nullable', 'mimes:pdf,mp4,m4v', 'max:102400'];
        }
        return $rules;
    }

    public function messages()
    {
        return [
            'document_id' => [
                'max' => __('validation.max')
            ],
            'document_name' => [
                'required' => __('validation.required'),
                'max' => __('validation.max')
            ],
            'file_id' => [
                'max' => __('validation.max')
            ],
            'document_file' => [
                'max' => __('validation.max'),
                'required' => __('validation.required'),
                'mimes' => __('validation.mimes')
            ],
            'document_contents' => [
                'max' => __('validation.max')
            ],
            'document_version' => [
                'required' => __('validation.required'),
                'max' => __('validation.max')
            ],
            'parent_document_id' => [
                'max' => __('validation.max')
            ],
            'start_date' => [
                'required' => __('validation.required'),
                'date' => __('validation.date')
            ],
            'end_date' => [
                'required' => __('validation.required'),
                'date' => __('validation.date')
            ],
            'available_org_cd' => [
                'max' => __('validation.max')
            ],
            'product_cd' => [
                'required' => __('validation.required'),
                'max' => __('validation.max')
            ],
            'disease' => [
                'max' => __('validation.max')
            ],
            'medical_subjects_group_cd' => [
                'max' => __('validation.max')
            ],
            'document_category_cd' => [
                'max' => __('validation.max')
            ],
            'safety_information_flag' => [
                'required' => __('validation.required'),
                'max' => __('validation.max')
            ],
            'off_label_information_flag' => [
                'required' => __('validation.required'),
                'max' => __('validation.max')
            ],
            'file_type' => [
                'required' => __('validation.required'),
                'max' => __('validation.max')
            ]
        ];
    }
}
