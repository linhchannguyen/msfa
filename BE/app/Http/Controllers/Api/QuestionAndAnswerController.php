<?php

namespace App\Http\Controllers\Api;

use App\Enums\QAEnum;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\QuestionAndAnswerManagementService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Traits\Base64StringFileTrait;
use App\Http\Requests\Api\QAManagement\QARequest;

class QuestionAndAnswerController extends Controller
{
    use Base64StringFileTrait;
    protected $service;
    public function __construct(QuestionAndAnswerManagementService $service) {
        $this->middleware('role.not:'.config('role.DEV.CODE'));
        $this->service = $service;
    }
    public function infoQA ($idQuestion, QARequest $request) {
        $userCdLogin = $this->getCurrentUser();
        $infoQuestion = $this->service->infoQuestion($idQuestion, $userCdLogin);
        if(isset($infoQuestion->file_url)){
            $infoQuestion->file_url = $this->encodeBase64String($infoQuestion->file_url);
        }
        return $this->responseSuccess('success', $infoQuestion);
    }
    public function listAnswerOfQuestion ($idQuestion, QARequest $request) {
        $limit = $request->get('limit', 100);
        $offset = $request->get('offset', 1);
        $userCdLogin = $this->getCurrentUser();
        $listAnswer = $this->service->listQuestion ($idQuestion, QAEnum::ANSWER_NORMAL, $userCdLogin, $limit, $offset);
        $listBestAnswer = $this->service->listQuestion ($idQuestion, QAEnum::ANSWER_BEST, $userCdLogin, $limit, $offset);
        foreach($listAnswer['records'] as $value){
            $value->file_url = $this->encodeBase64String($value->file_url);
        }
        foreach($listBestAnswer as $value){
            $value->file_url = $this->encodeBase64String($value->file_url);
        }
        return $this->responseSuccess('success', [
            'list_answer' => $listAnswer,
            'list_best_answer' => $listBestAnswer
        ]);
    }
    public function registerBestAnswer ($idAnswer, QARequest $request) {
        $userCdLogin = $this->getCurrentUser();
        $answer = $this->service->registerBestAnswer ($userCdLogin, $idAnswer);
        return $this->responseSuccess(__('messages.save_successfully'), $answer);
    }
    public function registerAnswer (QARequest $request) {
        $idQuestion = $request->get('question_id');
        $answerNote = $request->get('note');
        $userCdLogin = $this->getCurrentUser();
        $answer = $this->service->registerAnswer ($idQuestion, $userCdLogin, $answerNote);
        return $this->responseSuccess(__('messages.save_successfully'), $answer);
    }
    public function registerUnsuitableReportQuestion ($idQuestion, QARequest $request) {
        $userCdLogin = $this->getCurrentUser();
        $comment = $request->get('comment');
        $this->service->unsuitableReport ($userCdLogin, $comment, $idQuestion, QAEnum::QUESTION);
        return $this->responseSuccess(__('messages.save_successfully'), []);
    }
    public function registerUnsuitableReportAnswer ($idAnswer, QARequest $request) {
        $userCdLogin = $this->getCurrentUser();
        $comment = $request->get('comment');
        $this->service->unsuitableReport ($userCdLogin, $comment, $idAnswer, QAEnum::ANSWER);
        return $this->responseSuccess(__('messages.save_successfully'), []);
    }
    public function openAcceptAnswer($idQuestion, QARequest $request) {
        $this->service->openAcceptAnswer ($idQuestion);
        return $this->responseSuccess(__('messages.update_successfully'), []);
    }
    public function closeAcceptAnswer ($idQuestion, QARequest $request) {
        $this->service->closeAcceptAnswer ($idQuestion);
        return $this->responseSuccess(__('messages.update_successfully'), []);
    }
    public function registerPostingProhibited ($userCd, QARequest $request) {
        $userCdLogin = $this->getCurrentUser();
        $this->service->registerPostingProhibited ($userCd, $userCdLogin);
        return $this->responseSuccess(__('messages.update_successfully'), []);
    }
    // cancel unsuitable report
    public function cancelInformQuestion ($idQuestion, QARequest $request) {
        $userCdLogin = $this->getCurrentUser();
        $this->service->cancelInform ($userCdLogin, $idQuestion, QAEnum::QUESTION);
        return $this->responseSuccess(__('messages.update_successfully'), []);
    }
    public function cancelInformAnswer ($idAnswer, QARequest $request) {
        $userCdLogin = $this->getCurrentUser();
        $this->service->cancelInform ($userCdLogin, $idAnswer, QAEnum::ANSWER);
        return $this->responseSuccess(__('messages.update_successfully'), []);
    }
    // cancel all unsuitable report
    public function cancelInformAllQuestion ($idQuestion, QARequest $request) {
        $this->service->cancelInform ('', $idQuestion, QAEnum::QUESTION);
        return $this->responseSuccess(__('messages.update_successfully'), []);
    }
    public function cancelInformAllAnswer ($idAnswer, QARequest $request) {
        $this->service->cancelInform ('', $idAnswer, QAEnum::ANSWER);
        return $this->responseSuccess(__('messages.update_successfully'), []);
    }
    public function deleteQuestion ($idQuestion, QARequest $request) {
        $userCdLogin = $this->getCurrentUser();
        $flagDeletePhysics = $request->get('delete_physics', 0);
        $this->service->deleteQA ($userCdLogin, $idQuestion, QAEnum::QUESTION, $flagDeletePhysics);
        return $this->responseSuccess(
            $flagDeletePhysics ? __('messages.delete_successfully') : __('qa.delete_logic_qa_success'), 
        []);
    }
    public function deleteAnswer ($idAnswer, QARequest $request) {
        $userCdLogin = $this->getCurrentUser();
        $flagDeletePhysics = $request->get('delete_physics', 0);
        $this->service->deleteQA ($userCdLogin, $idAnswer, QAEnum::ANSWER, $flagDeletePhysics);
        return $this->responseSuccess(
            $flagDeletePhysics ? __('messages.delete_successfully') : __('qa.delete_logic_qa_success'), 
        []);
    }
    public function listUnsuitableReportQuestion ($idQuestion, QARequest $request) {
        return $this->service->listUnsuitableReport ($idQuestion, QAEnum::QUESTION);
    }
    public function listUnsuitableReportAnswer ($idAnswer, QARequest $request) {
        return $this->service->listUnsuitableReport ($idAnswer, QAEnum::ANSWER);
    }
}
