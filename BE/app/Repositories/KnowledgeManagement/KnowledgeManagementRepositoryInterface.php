<?php

namespace App\Repositories\KnowledgeManagement;

use App\Repositories\BaseRepositoryInterface;

interface KnowledgeManagementRepositoryInterface extends BaseRepositoryInterface
{
    public function getKnowledgeStatus();
    public function getPointTargetType();
    public function getActivePointNice();
    public function getActivePointComment();
    public function getKnowledgeCategory();
    public function getFacilityTypeGroup();
    public function getMedicalSubjectsGroup();
    public function getTopLike();
    public function search($conditions);
    public function getRequestUser($conditions);
    public function searchPre($conditions);
    public function knowledgeDetail($conditions);
    public function getKnowledgeTimeline($knowledge_id);
    public function getEvaluationComment($conditions);
    public function getTotalNice($knowledge_id);
    public function getUserApprovalKnowledgeAdmin($conditions);
    public function insertKnowledgeNice($datas);
    public function updateKnowledgeNice($datas);
    public function deleteKnowledgeNice($nice_id);
    public function getKnowledgeNice($knowledge_id, $nice_user_cd);
    public function getKnowledgeNiceById($nice_id);
    public function getKnowledge($conditions);
    public function insertActivePoint($datas);
    public function lastKnowledgeNice();
    public function getActivePoint($point_target_id, $point_target_type, $message);
    public function insertKnowledgeRequest($datas);
}