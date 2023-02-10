<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use App\Traits\ApiResponseTrait;
use App\Traits\DateTimeTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Traits\ValidateRequestTrait;

abstract class ApiBaseRequest extends FormRequest
{
    use ApiResponseTrait, DateTimeTrait,ValidateRequestTrait, ApiResponseTrait;

    /**
     * Declare date time field to auto correct
     */
    protected $dateTimeFields = [
        // 'field_1' => 'Y-m-d',
        // 'field_2' => 'Y-m-d H:i:s',
    ];

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function prepareForValidation()
    {
        $data = $this->toArray();

        // Auto correct all date format
        foreach($this->dateTimeFields as $field => $format) {
            isset($data[$field]) && $data[$field] = $this->correctDateTimeFormat($data[$field], $format);
        }

        $this->merge($data);
        /**
         * move param route id to param request , example router "/user/{id}"
         * @author huynh
         */
        if ( $this->route('id') != null ) {
            $this->merge(['id' => $this->route('id')]);
        }
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    abstract public function authorize();

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    abstract public function rules();

    /**
     * If validator fails return the exception in json form
     * @param Validator $validator
     * @return array
     */
    protected function failedValidation(Validator $validator)
    {
        $errors = (new ValidationException($validator))->errors();
        $message = $validator->errors()->first();
        
        throw new HttpResponseException($this->responseErrorValidate($message, $errors));
    }
}
