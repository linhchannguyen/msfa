<?php

namespace App\Services;

use App\Repositories\PersonDetailNote\PersonDetailNoteRepositoryInterface;
use App\Repositories\Inform\InformRepositoryInterface;

class PersonDetailNoteService
{
    private $repo, $inform;

    public function __construct(PersonDetailNoteRepositoryInterface $repo, InformRepositoryInterface $inform)
    {
        $this->repo = $repo;
        $this->inform = $inform;
    }

    public function allConsiderationType()
    {
        return $this->repo->allConsiderationType();
    }

    public function searchConsideration($conditions)
    {
        return $this->repo->searchConsideration($conditions);
    }

    public function getConsiderationById($id)
    {
        return $this->repo->getConsiderationById($id);
    }

    public function getConsiderationConfirmById($id)
    {
        return $this->repo->getConsiderationConfirmById($id);
    }

    public function considerationConfirm($conditions)
    {
        return $this->repo->considerationConfirm($conditions);
    }

    public function getPersonInChargeList($person_cd)
    {
        return $this->repo->getPersonInChargeList($person_cd);
    }

    public function createPersonConsideration($datas)
    {
        return $this->repo->createPersonConsideration($datas);
    }

    public function updatePersonConsideration($datas)
    {
        return $this->repo->updatePersonConsideration($datas);
    }

    public function createPersonConsiderationConfirm($datas)
    {
        return $this->repo->createPersonConsiderationConfirm($datas);
    }

    public function lastInsertedPersonConsideration()
    {
        return $this->repo->lastInsertedPersonConsideration();
    }

    public function createInform($datas)
    {
        $not_received_inform_list = $this->inform->installed($datas['user_cd_assign']);
        //Check if the user is set to receive notification type
        foreach($not_received_inform_list as $value){
            if($value->inform_category_cd == PERSON_DETAIL_NOTE_INFORM_CATEGORY && $value->checked == 0){
                return true;
            }
        }
        $inform_id = $this->inform->registerInform($datas['user_cd'], $datas['user_cd_assign'], $datas['inform_contents'], PERSON_DETAIL_NOTE_INFORM_CATEGORY);
        return $this->inform->registerInformParameter($datas['user_cd'], $inform_id, PERSON_CD, $datas['person_cd']);
    }

    public function deleteConsideration($id)
    {
        return $this->repo->deleteConsideration($id);
    }

    public function deleteConsiderationConfirm($conditions)
    {
        $result_2 = false;
        if(!empty($conditions['user_cd'])){
            $result_2 = $this->repo->deleteConsiderationConfirm($conditions);
        }else{
            $result_2 = true;
        }
        return $result_2;
    }
}