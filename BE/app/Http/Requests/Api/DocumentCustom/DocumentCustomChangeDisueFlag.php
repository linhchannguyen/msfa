<?php

namespace App\Http\Requests\Api\DocumentCustom;

use App\Http\Requests\Api\ApiBaseRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

class DocumentCustomChangeDisueFlag extends ApiBaseRequest {
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
    */
    public function rules() {
        
        $rules['id'] = ['nullable', 'exists:t_document_composition,document_id'];
        $rules['status'] = ['required', 'numeric', 'in:0,1'];
        

        return $rules;
    }

    public function messages() {
        return [
            'id.exists' => __('validation.exists', ['attribute' => 'ID']),
        ];
    }
}
