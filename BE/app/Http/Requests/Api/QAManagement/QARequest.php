<?php

namespace App\Http\Requests\Api\QAManagement;

use App\Enums\QAEnum;
use App\Http\Requests\Api\ApiBaseRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use App\Repositories\ManagerQuestionAndAwnser\ManagerQuestionAndAwnserInterface;
use App\Traits\AuthTrait;

class QARequest extends ApiBaseRequest {
    use AuthTrait;
    protected $repo;
    public function __construct(ManagerQuestionAndAwnserInterface $repo)
    {
        $this->repo = $repo;
    }
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
        $action = Route::currentRouteName();
        $userCdLogin = $this->getCurrentUser();
        // check id isset
        if ( 
            $action == 'E.api.manage-qa.unsuitable-report-question' || $action == 'E.api.manage-qa.list-answer' 
            || $action == 'E.api.manage-qa.list-unsuitable-report-question' || $action == 'E.api.manage-qa.delete-question' 
            || $action == 'E.api.manage-qa.cancel-inform-question' || $action == 'E.api.manage-qa.register-answer' 
            || $action == 'E.api.manage-qa.info-question' || $action == 'E.api.manage-qa.accept-answer' 
        ) {
            // check id isset
            $param = [
                [
                    "table" => "t_question",
                    "condition" => [
                        "question_id" => $this->id ? $this->id : ($this->question_id ? $this->question_id : null)
                    ]
                ]
            ];
            $this->validateInputParamater($param);
        }
        if ( 
            $action == 'E.api.manage-qa.unsuitable-report-answer' || $action == 'E.api.manage-qa.list-unsuitable-report-answer' 
            || $action == 'E.api.manage-qa.delete-answer' || $action == 'E.api.manage-qa.register-best-answer' 
            || $action == 'E.api.manage-qa.cancel-inform-answer'
        ) {
            $param = [
                [
                    "table" => "t_answer",
                    "condition" => [
                        "answer_id" => $this->id
                    ]
                ]
            ];
            $this->validateInputParamater($param);
        }


        if ( 
            $action == 'E.api.manage-qa.unsuitable-report-question' || $action == 'E.api.manage-qa.list-answer' 
            || $action == 'E.api.manage-qa.list-unsuitable-report-question'
        ) {
            $rules['id'] = ['nullable', 'exists:t_question,question_id'];
        }
        
        if ( $action == 'E.api.manage-qa.unsuitable-report-answer' || $action == 'E.api.manage-qa.list-unsuitable-report-answer' ) {
            $rules['id'] = ['nullable', 'exists:t_answer,answer_id'];
        }
        // delete question
        if ( $action == 'E.api.manage-qa.delete-question' ) {
            $rules['id'] = ['nullable', 'exists:t_question,question_id', function ($attribute, $value, $fail) use ($userCdLogin) {
                if ( !$this->repo->isUserManager ($userCdLogin) && !$this->repo->isUserCreateQA ($value, $userCdLogin, QAEnum::QUESTION) ) {
                    return $fail(config('messages.MSFA0033'));
                }
            }];
        }
        // cancel report question
        if ( $action == 'E.api.manage-qa.cancel-inform-question' || $action == 'E.api.manage-qa.cancel-all-inform-question') {
            $rules['id'] = ['nullable', 'exists:t_question,question_id', function ($attribute, $value, $fail) use ($userCdLogin) {
                if ( !$this->repo->isUserManager ($userCdLogin) && !$this->repo->isUserCreateReport($value, $userCdLogin) ) {
                    return $fail(config('messages.MSFA0033'));
                }
            }];
        }
        // delete answer
        if ( $action == 'E.api.manage-qa.delete-answer') {
            $rules['id'] = ['nullable', 'exists:t_answer,answer_id', function ($attribute, $value, $fail) use ($userCdLogin) {
                if ( !$this->repo->isUserManager ($userCdLogin) ) {
                    return $fail(config('messages.MSFA0033'));
                }
            }];
        }
        // cancel report answer
        if ( $action == 'E.api.manage-qa.cancel-inform-answer' || $action == 'E.api.manage-qa.cancel-all-inform-answer' ) {
            $rules['id'] = ['nullable', 'exists:t_answer,answer_id', function ($attribute, $value, $fail) use ($userCdLogin) {
                if ( !$this->repo->isUserManager ($userCdLogin) && !$this->repo->isUserCreateReport($value, $userCdLogin) ) {
                    return $fail(config('messages.MSFA0033'));
                }
            }];
        }
        if ( $action == 'E.api.manage-qa.register-posting-prohibited' ) {
            // $rules['id'] = ['nullable', 'exists:m_user,user_cd'];
            $rules['id'] = ['nullable', 'exists:m_user,user_cd', function ($attribute, $value, $fail) use ($userCdLogin) {
                if ( !$this->repo->isUserManager ($userCdLogin) ) {
                    return $fail(config('messages.MSFA0033'));
                }
            }];
        }
        
        if ( $action == 'E.api.manage-qa.register-answer' ) {
            $rules['question_id'] = ['required', 'exists:t_question,question_id', function ($attribute, $value, $fail) {
                // check status has stop comment 
                if ( $this->repo->isStopCommentByAnswer ($value) ) {
                    // return $fail('câu trả lời đã đóng cho câu hỏi này, vui lòng thử lại sau');
                    return $fail(__('qa.question_has_stop_can_not_comment'));
                }
            }];
            $rules['note'] = ['required'];
        }
        if ( $action == 'E.api.manage-qa.register-best-answer' ) {
            // $rules['id'] = ['nullable', 'exists:t_answer,answer_id', function ($attribute, $value, $fail) {
            $rules['id'] = ['nullable', 'exists:t_answer,answer_id', function ($attribute, $value, $fail) use ($userCdLogin) {
                // only allow user register question action
                if ( !$this->repo->isUserQuestion ($value, $userCdLogin) ) {
                    return $fail(config('messages.MSFA0033')); // chỉ có người tạo câu hỏi mới có quyền tạo câu trả lời tốt nhất
                }
                // check amount answer over amount answer allow
                if ( $amountLimitBestAnswer = $this->repo->isOverAmountAnswer ($value) ) {
                    return $fail('既に'.$amountLimitBestAnswer.'件のベストアンサーが登録されています。');
                }
            }];
        }
        if ( $action == 'E.api.manage-qa.unsuitable-report-question' || $action == 'E.api.manage-qa.unsuitable-report-answer' ) {
            $rules['comment'] = ['required', 'max:100'];
        }
        if ( $action == 'E.api.manage-qa.info-question' ) {
            $rules['id'] = ['nullable', 'exists:t_question,question_id'];
        }
        if ( $action == 'E.api.manage-qa.accept-answer' || $action == 'E.api.manage-qa.stop-accept-answer' ) {
            $rules['id'] = ['nullable', 'exists:t_question,question_id', function ($attribute, $value, $fail) use ($userCdLogin) {
                // only allow user register question action
                if ( !$this->repo->isUserOpenStopQuestion ($value, $userCdLogin) ) {
                    return $fail(config('messages.MSFA0033'));
                }
            }];
        }
        
        
        $rules['limit'] = ['nullable', 'numeric'];
        $rules['offset'] = ['nullable', 'numeric'];
        return $rules;
    }

    public function messages() {
        $action = Route::currentRouteName();
        if ( $action == 'E.api.manage-qa.info-question' ) {
            return [
                'id.exists' => __('qa.question_not_exist'),
            ];
        }
        return [
            'id.exists' => __('validation.exists', ['attribute' => 'ID']),
        ];
    }
}
