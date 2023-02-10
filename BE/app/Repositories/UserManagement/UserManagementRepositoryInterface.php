<?php

namespace App\Repositories\UserManagement;

use App\Repositories\BaseRepositoryInterface;

interface UserManagementRepositoryInterface extends BaseRepositoryInterface
{
    public function getVariableDefinition($definition_name);
    public function getApprovalLayerNum($parameter_name);
    public function getUserList($user_cd,$user_name,$org_cd,$date_system,$limit,$offset);

    public function getUserUpdate($user_cd,$start_date,$type);
    public function getDateUser($user_cd,$data_system);
    public function checkUserExist($user_cd,$start_date);
    public function createUser($user_cd,$user_name,$mail_address,$start_date,$end_date,$account_lock_flag,$account_lock_remarks);
    public function createPassphrase($user_cd, $passphrase);
    public function updateUser($user_cd,$start_date_old,$start_date,$end_date,$user_name,$mail_address,$account_lock_flag,$account_lock_remarks);
    public function updateMasterUser($user_cd, $user_name, $mail_address);
    public function updateEndDate($user_cd,$start_date,$end_date);
    public function deleteUser($user_cd,$start_date);

    public function getListUserOrganization($user_cd,$date_system);
    public function getListUserOrganizationReservation($user_cd,$date_system);

    public function getDateUserOrg($user_cd,$data_system);
    public function updateOrgEndDate($user_cd,$start_date,$end_date);
    public function getUserOrgUpdate($user_cd,$start_date,$type);
    public function deleteMasterUserOrgByUserCd($user_cd);
    public function updateMasterUserOrg($user_cd, $org_cd, $user_post_type, $main_flag);
    public function insertUserOrganization($user_cd,$start_date,$end_date,$org_cd,$main_flag,$user_post_type);
    public function deleteUserOrganization($user_cd,$start_date,$org_cd);

    public function getUserListRequest($user_cd,$user_name,$org_cd,$date_system,$limit,$offset);
    public function getOrgUserRequest($user_cd,$org_cd,$date_system);
    public function getApprovalUserCurrent($user_cd,$date_system,$approval_work_type,$parameter_value);
    public function getApprovalUserReservation($user_cd,$date_system,$approval_work_type,$parameter_value);
    
    public function getApprovalUserUpdate($user_cd,$approval_work_type,$start_date,$type);
    public function getDateApprovalUser($user_cd,$approval_work_type,$date_system);
    public function insertApprovalUser($request_user_cd,$approval_work_type,$start_date,$end_date,$approval_layer_num,$approval_user_cd);
    public function updateApprovalUserEndDate($user_cd,$approval_work_type,$start_date,$end_date);
    public function deleteApprovalUser($user_cd,$approval_work_type,$start_date,$approval_layer_num,$approval_user_cd);
}
