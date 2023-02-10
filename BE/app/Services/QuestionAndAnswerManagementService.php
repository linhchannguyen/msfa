<?php

namespace App\Services;

use App\Traits\DateTimeTrait;
use App\Repositories\ManagerQuestionAndAwnser\ManagerQuestionAndAwnserInterface;
use App\Enums\QAEnum;

class QuestionAndAnswerManagementService
{
    use DateTimeTrait;
    private $repo;

    public function __construct(ManagerQuestionAndAwnserInterface $repo)
    {
        $this->repo = $repo;
    }
    public function infoQuestion ($idQuestion, $userCdLogin) {
        return $this->repo->infomationQuestion ($idQuestion, $userCdLogin);
    }
    public function listQuestion ($idQuestion, $statusAnswer, $userCdLogin, $limit, $offset) {
        return $this->repo->infomationAnswer ($idQuestion, $statusAnswer, $userCdLogin, $limit, $offset);
    }
    public function registerBestAnswer ($userCdLogin, $idAnswer) {
        $typeAction = QAEnum::TYPE_ACTION_BEST_ANSWER;
        $idQuestion = $this->repo->getInfoAnswer ($idAnswer)->question_id;
        // change status to best answer
        $this->repo->changeFlagToBestAnswer ($idAnswer);
        // give point for user register answer
        $this->repo->givePointForUser ($userCdLogin, $typeAction, $idQuestion, $idAnswer);
        // inform to user register answer
        $this->repo->saveInform ($userCdLogin, $typeAction, $idQuestion, $idAnswer);
        return $this->repo->getInfoAnswer ($idAnswer);
    }
    public function registerAnswer ($idQuestion, $userCdLogin, $answerNote) {
        $typeAction = QAEnum::TYPE_ACTION_CREATE_ANSWER;
        // create answer 
        $idAnswer = $this->repo->createAnswer($idQuestion, $userCdLogin, $answerNote);
        // give point for user create question
        $this->repo->givePointForUser ($userCdLogin, $typeAction, $idQuestion);
        // inform to user register question
        $this->repo->saveInform ($userCdLogin, $typeAction, $idQuestion);
        return $this->repo->getInfoAnswer ($idAnswer);
    }
    public function unsuitableReport ($userCdLogin, $comment, $idQA, $typeQA) {
        $typeAction = QAEnum::TYPE_ACTION_UNSUITABLE_REPORT;
        // delete cancel report before
        $this->repo->deleteCancelInform($idQA, $typeQA);
        // create unsuitable report
        $this->repo->createUnsuitableReport ($userCdLogin, $comment, $idQA, $typeQA);
        // inform to manager
        if ( $typeQA == QAEnum::QUESTION ) {
            $idQuestion = $idQA;
            $idAnswer = '';
            $this->repo->saveInform ($userCdLogin, $typeAction, $idQA, '');
        }
        if ( $typeQA == QAEnum::ANSWER ) {
            $idQuestion = $this->repo->getInfoAnswer ($idQA)->question_id;
            $this->repo->saveInform ($userCdLogin, $typeAction, $idQuestion, $idQA);
        }
    }
    public function openAcceptAnswer ($idQuestion) {
        $this->repo->openAcceptAnswer ($idQuestion);
    }
    public function closeAcceptAnswer ($idQuestion) {
        $this->repo->closeAcceptAnswer ($idQuestion);
    }
    // userCd : user need prohibited
    public function registerPostingProhibited ($userCd, $userCdLogin) {
        $this->repo->registerPostingProhibited ($userCd, $userCdLogin);
    }
    public function cancelInform ($userCdLogin, $idQA, $typeQA) {
        $this->repo->cancelInform ($userCdLogin, $idQA, $typeQA);
    }
    public function deleteQA ($userCdLogin, $idQA, $typeQA, $flagDeletePhysics) {
        $this->repo->deleteQA ($idQA, $typeQA, $flagDeletePhysics);
        // $this->repo->cancelInform ('', $idQA, $typeQA);
    }
    public function listUnsuitableReport ($idQA, $typeQA) {
        return $this->repo->unsuitableReports ($idQA, $typeQA);
    }
}
