<?php

namespace App\Repositories\QAManagement;

use App\Repositories\BaseRepositoryInterface;

interface QAManagementRepositoryInterface extends BaseRepositoryInterface
{
    public function checkUserUnsuitableReport($user_cd);
    public function getQuestionStatus();
    public function getQuestionCategory();
    public function insert($datas);
    public function lastQuestion();
    public function insertQAInfoOrg($datas);
    public function insertActivePoint($datas);
    public function getQA($conditions);
}