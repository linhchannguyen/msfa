<?php

namespace App\Services;

use App\Repositories\PermissionSetting\PermissionSettingRepositoryInterface;
use App\Repositories\UserManagement\UserManagementRepositoryInterface;
use App\Traits\ArrayTrait;
use App\Traits\DateTimeTrait;

class PermissionSettingService
{
    use ArrayTrait, DateTimeTrait;
    private $repo, $user;

    public function __construct(PermissionSettingRepositoryInterface $repo, UserManagementRepositoryInterface $user)
    {
        $this->repo = $repo;
        $this->user = $user;
    }

    public function getScreenData()
    {
        $data = [];
        $data['role'] = $this->repo->getRole();
        $data['director'] = $this->repo->getDirector();
        return $data;
    }

    public function getRoleList($user_cd, $reference_date)
    {
        return $this->repo->getRoleListFromCurrent($user_cd, $reference_date);
    }

    public function getPermissionList($user_name, $user_cd, $org_cd, $reference_date, $director, $limit, $offset)
    {
        $date_system = $this->currentJapanDateTime("Y-m-d");
        $return = $result = [];
        $data = $this->repo->getPermissionList($user_name, $user_cd, $org_cd, $reference_date, $director, $limit, $offset);

        $user_cd = array_column($data['records'], 'user_cd');
        if (count($data['records']) > 0) {
            $user_org_active = $this->user->getListUserOrganization($user_cd, $date_system);
            $getRoleList = $this->repo->getRoleList($user_cd, $reference_date);
            foreach ($data['records'] as $dataItem) {
                $dataItem = (object)$dataItem;
                $dataItem->organization = [];
                $dataItem->user_role = [];
                if (count($user_org_active) > 0) {
                    foreach ($user_org_active as $org_active) {
                        $org_active = (object)$org_active;
                        if ($dataItem->user_cd === $org_active->user_cd) {
                            $organization = !empty($org_active->organization) ? json_decode($org_active->organization) : [];
                            array_multisort(array_column($organization, 'main_flag'), SORT_DESC, $organization);
                            $dataItem->organization = $organization;
                        }
                    }
                }

                if (count($getRoleList) > 0) {
                    foreach ($getRoleList as $roleList) {
                        $reservation_total = [];
                        if ($roleList->user_cd === $dataItem->user_cd) {
                            $user_role = !empty($roleList->user_role) ? json_decode($roleList->user_role) : [];
                            if (count($user_role) > 0) {
                                $start_date = array_column($user_role, 'start_date');
                                $start_date = array_unique($start_date);
                            }
                            if (count((array)$start_date) > 0) {
                                foreach ($start_date as $start_date) {
                                    $reservation_total[$start_date] = ['start_date' => $start_date];
                                }
                                array_multisort(
                                    array_column($reservation_total, 'start_date'),
                                    SORT_ASC,
                                    $reservation_total
                                );
                            }
                            $arr_tmp = [];
                            foreach ($reservation_total as &$_tmp1) {
                                $arr_tmp = [];
                                foreach ($user_role as $item_role) {
                                    if ($_tmp1['start_date'] == $item_role->start_date) {
                                        $reservation_total[$item_role->start_date]['end_date'] = $item_role->end_date;
                                        array_push($arr_tmp, [
                                            'role' => $item_role->role,
                                            'role_name' => $item_role->role_name,
                                            'role_short_name' => $item_role->role_short_name
                                        ]);
                                    }
                                }
                                $_tmp1['role'] = $arr_tmp;
                            }
                            $dataItem->user_role = array_values($reservation_total);
                        }
                    }
                }
                $result[] = [
                    'user_cd' => $dataItem->user_cd,
                    'user_name' => $dataItem->user_name,
                    'organization' => $dataItem->organization ?? [],
                    'user_role' => $dataItem->user_role
                ];
            }
        }

        $return['records'] = $result;
        $return['pagination'] = $data['pagination'];

        return $return;
    }

    public function deletePermission($user_cd, $start_date)
    {
        $this->repo->deletePermission($user_cd, $start_date);
    }

    public function updatePermission($data)
    {
        foreach ($data->datas as $value) {
            $this->repo->deletePermission($value['user_cd'], isset($value['start_date_old']) ? $value['start_date_old'] : $value['start_date']);
        }
        return $this->repo->updatePermission($data);
    }

    public function updateMasterUserRole($data)
    {
        $this->repo->updateMasterUserRole($data);
    }

    public function getCurrenntPermission($user_cd, $start_date)
    {
        return $this->repo->getCurrenntPermission($user_cd, $start_date, 'DESC');
    }

    public function updatePermissionEndDate($user_cd, $start_date, $end_date)
    {
        return $this->repo->updatePermissionEndDate($user_cd, $start_date, $end_date);
    }
}
