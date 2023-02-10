<?php

namespace App\Repositories\PersonGroup;

use App\Repositories\BaseRepositoryInterface;

interface PersonGroupRepositoryInterface extends BaseRepositoryInterface
{
    public function allData($request);
    public function listFacilityType($select_person_group_id);
    public function allPerson($select_person_group_id,$facility_category_type);
    public function deletePersonGroup($select_person_group_id);
    public function deletePersonGroupContent($select_person_group_id);
    public function deletePersonGroupDocument($select_person_group_id);
    public function deletePersonGroupProduct($select_person_group_id);
    public function updatePersonGroup($data);
    public function insertPersonGroup($data);
    public function updateGroupContent($user_cd,$person_group_id,$content_cd,$facility_category_type);
    public function updateGroupProduct($user_cd,$person_group_id,$product_cd,$facility_category_type);
    public function updateGroupDocument($user_cd,$person_group_id,$document,$facility_category_type);
    public function deleteGroupDetail($person_group_id);
    public function insertGroupDetail($user_cd,$select_person_group_id,$facility_cd,$person_cd);
}
