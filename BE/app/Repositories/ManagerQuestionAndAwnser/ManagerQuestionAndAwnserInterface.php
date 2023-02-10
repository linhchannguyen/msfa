<?php

namespace App\Repositories\ManagerQuestionAndAwnser;

use App\Repositories\BaseRepositoryInterface;

interface ManagerQuestionAndAwnserInterface extends BaseRepositoryInterface
{
    public function infomationQuestion ($idQuestion, $userCdLogin);
    public function infomationAnswer ($idQuestion, $statusAnswer, $userCdLogin, $limit = 10, $offset = 1);
    public function changeFlagToBestAnswer ($idAnswer);
    public function givePointForUser ($userCdLogin, $typeAction, $idQuestion, $idAnswer = '');
    public function saveInform ($userCdLogin, $typeAction, $idQuestion, $idAnswer = '');
    public function installed($user_cd, $inform_category_cd = null);
    // public function saveInformParameter ($userCdLogin, $idQuestion);
    public function createAnswer($idQuestion, $userCdLogin, $answerNote);
    public function createUnsuitableReport ($userCdLogin, $comment, $idQA, $typeQA);
    public function openAcceptAnswer ($idQuestion);
    public function closeAcceptAnswer ($idQuestion);
    public function registerPostingProhibited ($userCd, $userCdLogin);
    public function cancelInform ($userCdLogin, $idQA, $typeQA);
    /**
     * delete cancel report
     */
    public function deleteCancelInform ($idQA, $typeQA);
    public function getInfoAnswer ($idAnswer);
    public function deleteQA ($idQA, $typeQA, $flagDeletePhysics);
    public function isStopCommentByAnswer ($idAnswer);
    public function isOverAmountAnswer ($idAnswer);
    public function isUserQuestion ($idAnswer, $userCdLogin);
    public function isUserOpenStopQuestion ($idQuestion, $userCdLogin);
    public function isUserManager ($userCdLogin);
    public function unsuitableReports ($idQA, $typeQA);
    public function isUserCreateQA ($idQA, $userCdLogin, $typeQA);
    public function isUserCreateReport ($idQA, $userCdLogin);
    public function givePointToUser ($userCdLogin, $typeAction, $targetId);
}
