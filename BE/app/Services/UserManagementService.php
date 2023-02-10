<?php

namespace App\Services;

use App\Repositories\UserManagement\UserManagementRepositoryInterface;
use Carbon\Carbon as time;
use App\Services\SystemParameterService;
use App\Services\OrganizationService;
use App\Traits\ArrayTrait;

class UserManagementService
{
    use ArrayTrait;
    private $repo;
    public function __construct(UserManagementRepositoryInterface $repo, SystemParameterService $system, OrganizationService $organization)
    {
        $this->repo = $repo;
        $this->system = $system;
        $this->organization = $organization;
    }

    public function getUserList($user_cd, $user_name, $org_cd, $limit, $offset)
    {
        $date_system = $this->getDateSystem();
        // get user in active
        $data = $this->repo->getUserList($user_cd, $user_name, $org_cd, $date_system, $limit, $offset);

        if (count((array)$data['records']) > 0) {
            foreach ($data['records'] as $item) {
                $item = (object)$item;
                $item->advance_reservation = !empty($item->advance_reservation) ? json_decode($item->advance_reservation) : [];
                $item->user_name = null;
                $item->start_date = null;
                $item->end_date = null;
                $item->mail_address = null;
                $item->account_lock_flag = 0;
                $item->account_lock_remarks = null;
                if(!empty($item->current_user_system)){
                    $current_user_system = json_decode($item->current_user_system);
                    $item->user_name = $current_user_system[0]->user_name;
                    $item->start_date = date_format(date_create($current_user_system[0]->start_date),'Y/m/d');
                    $item->end_date = date_format(date_create($current_user_system[0]->end_date),'Y/m/d');
                    $item->mail_address = $current_user_system[0]->mail_address;
                    $item->account_lock_flag = $current_user_system[0]->account_lock_flag;
                    $item->account_lock_remarks = $current_user_system[0]->account_lock_remarks;
                }
                unset($item->current_user_system);
                if(count($item->advance_reservation) > 0){
                    foreach($item->advance_reservation as $advance_reservation_item){
                        // format end_date , start_date
                        $advance_reservation_item->start_date = date_format(date_create($advance_reservation_item->start_date),'Y/m/d');
                        $advance_reservation_item->end_date = date_format(date_create($advance_reservation_item->end_date),'Y/m/d');
                    }
                }
            }
        }
        return $data;
    }

    public function getListUserOrganization($user_cd, $user_name, $org_cd, $limit, $offset)
    {
        $result = $return = [];
        $date_system = $this->getDateSystem();
        // get user org in active
        $datas = $this->repo->getUserList($user_cd, $user_name, $org_cd, $date_system, $limit, $offset);
        $user_cd = array_column($datas['records'], 'user_cd');
        if (count((array)$datas['records'])) {
            $user_org_active = $this->repo->getListUserOrganization($user_cd, $date_system);
            $user_org_reservation = $this->repo->getListUserOrganizationReservation($user_cd, $date_system);
            foreach ($datas['records'] as $data) {
                $data = (object)$data;
                $advance_reservation = !empty($data->advance_reservation) ? json_decode($data->advance_reservation) : [];
                $ar_user_name = count($advance_reservation) > 0 ? $advance_reservation[0]->user_name : null;
                $data->user_name = null;
                $current_user_system = !empty($data->current_user_system) ? json_decode($data->current_user_system) : [];
                $data->user_name = !empty($current_user_system[0]->user_name) ? $current_user_system[0]->user_name : $ar_user_name;
                unset($data->current_user_system);
                //active
                if (count((array)$user_org_active)) {
                    $org_active_db = [];
                    foreach ($user_org_active as $org_active) {
                        $org_active = (object)$org_active;
                        if ($data->user_cd == $org_active->user_cd) {
                            $organization = !empty($org_active->organization) ? json_decode($org_active->organization) : [];
                            array_multisort(array_column($organization, 'main_flag'),SORT_DESC,$organization);
                            $org_active_db[] = [
                                'start_date' => $org_active->start_date,
                                'end_date' => $org_active->end_date,
                                'organization' => $organization
                            ];
                        }
                    }
                }
                //advance_reservation
                if (count((array)$user_org_reservation)) {
                    $org_reservation_db = [];
                    foreach ($user_org_reservation as $org_reservation) {
                        $org_reservation = (object)$org_reservation;
                        if ($data->user_cd == $org_reservation->user_cd) {
                            $organization = !empty($org_reservation->organization) ? json_decode($org_reservation->organization) : [];
                            array_multisort(array_column($organization, 'main_flag'),SORT_DESC,$organization);
                            $org_reservation_db[] = [
                                'start_date' => $org_reservation->start_date,
                                'end_date' => $org_reservation->end_date,
                                'organization' => $organization
                            ];
                        }
                    }
                }
                $result[] = [
                    'user_cd' => $data->user_cd,
                    'user_name' => $data->user_name,
                    'active' => $org_active_db ?? [],
                    'advance_reservation' => $org_reservation_db ?? []
                ];
            }
        }

        $return['records'] = $result;
        $return['pagination'] = $datas['pagination'];

        return $return;
    }

    public function createUser($user_cd, $user_name, $mail_address, $start_date, $end_date, $account_lock_flag, $account_lock_remarks)
    {
        return $this->repo->createUser($user_cd, $user_name, $mail_address, $start_date, $end_date, $account_lock_flag, $account_lock_remarks);
    }

    public function createPassphrase($user_cd, $passphrase)
    {
        return $this->repo->createPassphrase($user_cd, $passphrase);
    }

    public function deleteUser($user_cd, $start_date)
    {
        return $this->repo->deleteUser($user_cd, $start_date);
    }

    public function checkUserExist($user_cd, $start_date)
    {
        return  $this->repo->checkUserExist($user_cd, $start_date);
    }

    public function getDateUser($user_cd, $data_system)
    {
        return  $this->repo->getDateUser($user_cd, $data_system);
    }

    public function getDateUserOrg($user_cd, $data_system)
    {
        return  $this->repo->getDateUserOrg($user_cd, $data_system);
    }

    public function getDateApprovalUser($user_cd, $approval_work_type, $date_system)
    {
        return $this->repo->getDateApprovalUser($user_cd, $approval_work_type, $date_system);
    }

    public function getDateSystem()
    {
        $data_system = $this->system->getCurrentSystemDateTime();
        return $data_system ? date_create($data_system)->format('Y-m-d') : time::now()->toDateString();
    }

    public function updateUser($user_cd, $start_date_old, $start_date, $end_date, $user_name, $mail_address, $account_lock_flag, $account_lock_remarks)
    {
        return $this->repo->updateUser($user_cd, $start_date_old, $start_date, $end_date, $user_name, $mail_address, $account_lock_flag, $account_lock_remarks);
    }

    public function updateMasterUser($user_cd, $user_name, $mail_address)
    {
        return $this->repo->updateMasterUser($user_cd, $user_name, $mail_address);
    }

    public function deleteMasterUserOrgByUserCd($user_cd)
    {
        return $this->repo->deleteMasterUserOrgByUserCd($user_cd);
    }

    public function updateMasterUserOrg($user_cd, $org_cd, $user_post_type, $main_flag)
    {
        return $this->repo->updateMasterUserOrg($user_cd, $org_cd, $user_post_type, $main_flag);
    }

    public function updateEndDate($user_cd, $start_date, $end_date)
    {
        return $this->repo->updateEndDate($user_cd, $start_date, $end_date);
    }

    public function updateOrgEndDate($user_cd, $start_date, $end_date)
    {
        return $this->repo->updateOrgEndDate($user_cd, $start_date, $end_date);
    }

    public function updateApprovalUserEndDate($user_cd, $approval_work_type, $start_date, $end_date)
    {
        return $this->repo->updateApprovalUserEndDate($user_cd, $approval_work_type, $start_date, $end_date);
    }

    public function getUserUpdate($user_cd, $start_date)
    {
        return $this->repo->getUserUpdate($user_cd, $start_date, 'DESC');
    }

    public function getUserOrgUpdate($user_cd, $start_date)
    {
        return $this->repo->getUserOrgUpdate($user_cd, $start_date, 'DESC');
    }

    public function getApprovalUserUpdate($user_cd, $approval_work_type, $start_date)
    {
        return $this->repo->getApprovalUserUpdate($user_cd, $approval_work_type, $start_date, 'DESC');
    }

    public function deleteUserOrganization($user_cd, $start_date, $org_cd)
    {
        return $this->repo->deleteUserOrganization($user_cd, $start_date, $org_cd);
    }

    public function deleteApprovalUser($user_cd, $approval_work_type, $start_date, $approval_layer_num, $approval_user_cd)
    {
        return $this->repo->deleteApprovalUser($user_cd, $approval_work_type, $start_date, $approval_layer_num, $approval_user_cd);
    }

    public function insertUserOrganization($user_cd, $start_date, $end_date, $org_cd, $main_flag, $user_post_type)
    {
        return $this->repo->insertUserOrganization($user_cd, $start_date, $end_date, $org_cd, (bool)$main_flag, $user_post_type);
    }

    public function insertApprovalUser($request_user_cd, $approval_work_type, $start_date, $end_date, $approval_layer_num, $approval_user_cd)
    {
        return $this->repo->insertApprovalUser($request_user_cd, $approval_work_type, $start_date, $end_date, $approval_layer_num, $approval_user_cd);
    }

    public function getScreenData()
    {
        $result['user_post_type'] = $this->repo->getVariableDefinition('ユーザ役職区分');
        $result['approval_title'] = $this->repo->getVariableDefinition('承認業務区分');
        $result['approval_layer_num'] = $this->repo->getApprovalLayerNum('承認階層');
        return $result;
    }

    public function getApprovalUser($user_cd, $user_name, $org_cd, $approval_work_type, $parameter_value, $limit, $offset)
    {
        $return = $result = [];
        $date_system = $this->getDateSystem();
        $datas = $this->repo->getUserListRequest($user_cd, $user_name, $org_cd, $date_system, $limit, $offset);
        $user_cd = array_column($datas['records'], 'user_cd');
        if (count($datas['records']) > 0) {
            $data_org = $this->repo->getOrgUserRequest($user_cd, $org_cd, $date_system);
            $approval_user_current = $this->repo->getApprovalUserCurrent($user_cd, $date_system, $approval_work_type, $parameter_value);
            $approval_user_reservation = $this->repo->getApprovalUserReservation($user_cd, $date_system, $approval_work_type, $parameter_value);
            foreach ($datas['records'] as $item) {
                $item = (object)$item;
                $item->org_label = $item->org_cd = $item->definition_label = '';
                $advance_reservation = !empty($item->advance_reservation) ? json_decode($item->advance_reservation) : [];
                $ar_user_name = count($advance_reservation) > 0 ? $advance_reservation[0]->user_name : null;
                $item->user_name = null;
                $current_user_system = !empty($item->current_user_system) ? json_decode($item->current_user_system) : [];
                $item->user_name = !empty($current_user_system[0]->user_name) ? $current_user_system[0]->user_name : $ar_user_name;
 
                if (count($data_org) > 0) {
                    foreach ($data_org as $org_item) {
                        if ($item->user_cd == $org_item->user_cd) {
                            if(!empty($org_item->org_current)){
                                $org_current_temp = json_decode($org_item->org_current);
                                $item->org_label = $org_current_temp->org_label;
                                $item->org_cd = $org_current_temp->org_cd;
                                $item->definition_label = $org_current_temp->definition_label;
                            }elseif(!empty($org_item->org_reservation)){
                                $org_current_temp = json_decode($org_item->org_reservation);
                                $item->org_label = $org_current_temp->org_label;
                                $item->org_cd = $org_current_temp->org_cd;
                                $item->definition_label = $org_current_temp->definition_label;
                            }
                        }
                    }
                }

                $approval_current = $approval_reservation = [];
                if (count((array)$approval_user_current)) {
                    foreach ($approval_user_current as $approval_user) {
                        $approval_user = (object)$approval_user;
                        $total = [];
                        $approval_layer_num = [];
                        if ($item->user_cd == $approval_user->request_user_cd) {
                            $approval_user->approval_layer_num = !empty($approval_user->approval_layer_num) ? json_decode($approval_user->approval_layer_num) : [];
                            if (count($approval_user->approval_layer_num) > 0) {
                                $approval_layer_num = array_column($approval_user->approval_layer_num, 'approval_layer_num');
                                $approval_layer_num = array_unique($approval_layer_num);
                            }
                            if (count($approval_layer_num) > 0) {
                                foreach ($approval_layer_num as $layer_num) {
                                    $total[$layer_num] = ['approval_layer_num' => $layer_num, 'approval_user' => []];
                                }
                            }

                            foreach ($approval_user->approval_layer_num as $app_layer_num) {
                                $total[$app_layer_num->approval_layer_num]['approval_user'][] = [
                                    'approval_user_cd' => $app_layer_num->approval_user_cd,
                                    'user_name' => $app_layer_num->user_name,
                                    'org_label' => $app_layer_num->org_label,
                                    'org_cd' => $app_layer_num->org_cd,
                                    'definition_label' => $app_layer_num->definition_label
                                ];

                                array_multisort(
                                    array_column($total[$app_layer_num->approval_layer_num]['approval_user'], 'approval_user_cd'),
                                    SORT_ASC,
                                    $total[$app_layer_num->approval_layer_num]['approval_user']
                                );
                            }

                            ksort($total);
                            $total = array_values($total);

                            $approval_current[] = [
                                'start_date' => $approval_user->start_date,
                                'end_date' => $approval_user->end_date,
                                'approval_layer_num' => $total
                            ];
                        }
                    }
                }

                if (count((array)$approval_user_reservation)) {
                    foreach ($approval_user_reservation as $reservation) {
                        $reservation = (object)$reservation;
                        if ($reservation->request_user_cd == $item->user_cd) {
                            $reservation_total = [];
                            $reservation_layer_num = [];
                            $reservation->approval_layer_num = !empty($reservation->approval_layer_num) ? json_decode($reservation->approval_layer_num) : [];
                            if (count($reservation->approval_layer_num) > 0) {
                                $reservation_layer_num = array_column($reservation->approval_layer_num, 'approval_layer_num');
                                $reservation_layer_num = array_unique($reservation_layer_num);
                            }
                            if (count($reservation_layer_num) > 0) {
                                foreach ($reservation_layer_num as $layer_num) {
                                    $reservation_total[$layer_num] = ['approval_layer_num' => $layer_num, 'approval_user' => []];
                                }
                            }
                            foreach ($reservation->approval_layer_num as $app_layer_num) {
                                $reservation_total[$app_layer_num->approval_layer_num]['approval_user'][] = [
                                    'approval_user_cd' => $app_layer_num->approval_user_cd,
                                    'user_name' => $app_layer_num->user_name,
                                    'definition_label' => $app_layer_num->definition_label,
                                    'org_label' => $app_layer_num->org_label,
                                    'org_cd' => $app_layer_num->org_cd
                                ];

                                array_multisort(
                                    array_column($reservation_total[$app_layer_num->approval_layer_num]['approval_user'], 'approval_user_cd'),
                                    SORT_ASC,
                                    $reservation_total[$app_layer_num->approval_layer_num]['approval_user']
                                );
                            }
                            ksort($reservation_total);
                            $reservation_total = array_values($reservation_total);
                            $approval_reservation[] = [
                                'start_date' => $reservation->start_date,
                                'end_date' => $reservation->end_date,
                                'approval_layer_num' => $reservation_total
                            ];
                        }
                    }
                }

                $result[] = [
                    'user_cd' => $item->user_cd,
                    'user_name' => $item->user_name,
                    'org_label' => $item->org_label,
                    'org_cd' => $item->org_cd,
                    'definition_label' => $item->definition_label,
                    'approval_current' => $approval_current ?? [],
                    'approval_reservation' => $approval_reservation ?? []
                ];
            }
        }

        $return['records'] = $result;
        $return['pagination'] = $datas['pagination'];
        return $return;
    }
}
