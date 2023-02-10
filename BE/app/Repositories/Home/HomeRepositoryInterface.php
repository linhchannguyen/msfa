<?php

namespace App\Repositories\Home;

use App\Repositories\BaseRepositoryInterface;

interface HomeRepositoryInterface extends BaseRepositoryInterface
{
    //Get widget list
    public function widget();

    //Get Organization layer
    public function getOrganizationLayer();

    //Get all actual digestion items
    public function getActualDigestedItems();

    //Get message list
    public function all($user_cd);

    //Check read message
    public function checkRead($data);

    //Read message
    public function readMessage($data);

    //Get Inform list
    public function informList($user_cd);

    //Informed
    public function informed($user_cd);

    //Count inform
    public function countInform($user_cd);

    //Get activity plan
    public function getActivityPlan($conditions);

    public function getVisitActivityPlan($schedule_id);

    public function getBriefingActivityPlan($schedule_id);

    public function getConventionActivityPlan($schedule_id);

    //Get external url
    public function getExternalUrlLink();

    //Get external embedded url
    public function getExternalEmbeddedUrl();

    public function getSetPeriod($conditions);

    public function getPeriodConfirmation();

    //Get daily report un-submitted
    public function getDailyReportUnsubmitted($conditions);

    //Get daily report un-approved
    public function getDailyReportUnApproved($conditions);

    //Get daily report remand
    public function getDailyReportRemand($conditions);

    //Get briefing un-approved
    public function getBriefingUnApproved($conditions);

    //Get briefing remand
    public function getBriefingRemand($conditions);

    //Get convention un-approved
    public function getConventionUnApproved($conditions);

    //Get convention remand
    public function getConventionRemand($conditions);

    //Get knowledge un-approved
    public function getKnowledgeUnApproved($conditions);

    //Get knowledge remand
    public function getKnowledgeRemand($conditions);

    //Get Inappropriate Report
    public function getInappropriateReport();

    //Get person note consideration un-confirm
    public function getPersonConsiderationUnConfirm($conditions);

    //Get facility note consideration un-confirm
    public function getFacilityConsiderationUnConfirm($conditions);

    public function actualDigestionRanking($conditions);

    public function getTimeSalesResults($conditions);

    public function sameAsBeforeSalesResults($conditions);

    public function actualDigestionProcess($conditions);
}
