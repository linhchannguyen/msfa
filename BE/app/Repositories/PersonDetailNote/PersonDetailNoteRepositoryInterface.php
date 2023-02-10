<?php

namespace App\Repositories\PersonDetailNote;

use App\Repositories\BaseRepositoryInterface;

interface PersonDetailNoteRepositoryInterface extends BaseRepositoryInterface
{
    public function allConsiderationType();
    public function searchConsideration($conditions);
    public function considerationConfirm($conditions);
    public function getPersonInChargeList($person_cd);
    public function createPersonConsideration($datas);
    public function updatePersonConsideration($datas);
    public function createPersonConsiderationConfirm($datas);
    public function lastInsertedPersonConsideration();
    public function getConsiderationById($id);
    public function getConsiderationConfirmById($id);
    public function deleteConsideration($id);
    public function deleteConsiderationConfirm($conditions);
}
