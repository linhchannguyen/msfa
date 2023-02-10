<?php

namespace App\Services;

use App\Repositories\FacilityUser\FacilityUserRepositoryInterface;
use App\Services\LogService;

class FacilityUserService
{
    private $repo;
    protected $serviceAccessLog;
    protected $arr_check = [];

    public function __construct(FacilityUserRepositoryInterface $repo, LogService $serviceAccessLog, SystemParameterService $system)
    {
        $this->date = $system->getCurrentSystemDateTime();
        $this->repo = $repo;
        $this->serviceAccessLog = $serviceAccessLog;
    }
    /**
     * add master equipment history table h_facility_user
     * 
     * @author huynh
     */
    public function addMasterEquipmentHistory($jobId, $keyBatchJob)
    {
        return $this->repo->addMasterEquipmentHistoryPerson($jobId, $keyBatchJob);
    }
    /**
     * add master equipment history person table h_facility_user
     * 
     * @author huynh
     */
    public function addMasterEquipmentHistoryFromEnclave($jobId, $keyBatchJob)
    {
        return $this->repo->addMasterEquipmentHistoryPersonFromEnclave($jobId, $keyBatchJob);
    }
    /**
     * update end_date table h_facility_user
     * 
     * @author huynh
     */
    public function updateEndDatePersonChargeArea($jobId = '', $keyBatchJob = '')
    {
        $insert_datas = [];
        $masterEquipments = $this->repo->getAllMasterEquipmentPerson();
        // save info before
        $this->serviceAccessLog->logInfoContent($jobId, $keyBatchJob, "updated successfully h_facility_user from h_enclave_user data");
        foreach ($masterEquipments as $masterEquipment) {
            $this->repo->deleteMasterEquipmentPerson($masterEquipment->facility_cd);
            $facility_users = empty($masterEquipment->facility_user) ? [] : json_decode($masterEquipment->facility_user, true);
            if (empty($facility_users)) {
                $insert_datas[] = [
                    'facility_cd' => $masterEquipment->facility_cd, 'user_cd' => $masterEquipment->user_cd, 'start_date' => $masterEquipment->start_date, 'end_date' => $masterEquipment->end_date, 'main_flg' => 1, 'created_by' => 'I03-B03', 'created_at' => $this->date, 'updated_by' => 'I03-B03', 'updated_at' => $this->date
                ];
            } else {
                $user_cd_exist = array_search($masterEquipment->user_cd, array_column($facility_users, 'user_cd'));
                if ($user_cd_exist !== false) {
                    $facility_users[$user_cd_exist]['facility_cd'] = $masterEquipment->facility_cd;
                    $facility_users[$user_cd_exist]['user_cd'] = $masterEquipment->user_cd;
                    $facility_users[$user_cd_exist]['start_date'] = $masterEquipment->start_date;
                    $facility_users[$user_cd_exist]['end_date'] = $masterEquipment->end_date;
                } else {
                    array_push($facility_users, [
                        'facility_cd' => $masterEquipment->facility_cd, 'user_cd' => $masterEquipment->user_cd, 'start_date' => $masterEquipment->start_date, 'end_date' => $masterEquipment->end_date
                    ]);
                }
                array_multisort(
                    array_column($facility_users, 'start_date'),
                    SORT_ASC,
                    $facility_users
                );
                $this->arr_check = $facility_users;
                $datas = [];
                foreach ($facility_users as $values) {
                    $facility_user_new = $this->__checkDuplicate($values);
                    $user_cd_exist = array_search($values['user_cd'], array_column($datas, 'user_cd'));
                    if ($user_cd_exist !== false) {
                        continue;
                    }
                    array_push($datas, [
                        'facility_cd' => $values['facility_cd'], 'user_cd' => $values['user_cd'], 'start_date' => $facility_user_new[0]['start_date'], 'end_date' => $facility_user_new[count($facility_user_new) - 1]['end_date']
                    ]);
                }
                $user_cd_position = array_search($masterEquipment->user_cd, array_column($datas, 'user_cd'));
                if (count($datas) > 1) {
                    if ($user_cd_position == 0) {
                        $insert_datas[] = [
                            'facility_cd' => $datas[0]['facility_cd'], 'user_cd' => $datas[0]['user_cd'], 'start_date' => $datas[0]['start_date'], 'end_date' => $datas[0]['end_date'], 'main_flg' => 1, 'created_by' => 'I03-B03', 'created_at' => $this->date, 'updated_by' => 'I03-B03', 'updated_at' => $this->date
                        ];
                        if ($datas[0]['start_date'] >= $datas[1]['end_date']) {
                            $datas[1]['end_date'] = date_create($datas[0]['start_date'])->modify('-1 days')->format('Y-m-d');
                            if ($datas[1]['end_date'] < $datas[1]['start_date']) {
                                // Data invalid
                            } else {
                                $insert_datas[] = [
                                    'facility_cd' => $datas[1]['facility_cd'], 'user_cd' => $datas[1]['user_cd'], 'start_date' => $datas[1]['start_date'], 'end_date' => $datas[1]['end_date'], 'main_flg' => 1, 'created_by' => 'I03-B03', 'created_at' => $this->date, 'updated_by' => 'I03-B03', 'updated_at' => $this->date
                                ];
                            }
                        } else {
                            if ($datas[0]['end_date'] < $datas[1]['end_date']) {
                                $datas[2]['start_date'] = date_create($datas[0]['end_date'])->modify('+1 days')->format('Y-m-d');
                                $datas[2]['end_date'] = $datas[1]['end_date'];
                                $datas[1]['end_date'] = date_create($datas[0]['start_date'])->modify('-1 days')->format('Y-m-d');
                                if ($datas[1]['end_date'] < $datas[1]['start_date']) {
                                    // Data invalid
                                } else {
                                    $insert_datas[] = [
                                        'facility_cd' => $datas[1]['facility_cd'], 'user_cd' => $datas[1]['user_cd'], 'start_date' => $datas[1]['start_date'], 'end_date' => $datas[1]['end_date'], 'main_flg' => 1, 'created_by' => 'I03-B03', 'created_at' => $this->date, 'updated_by' => 'I03-B03', 'updated_at' => $this->date
                                    ];
                                    $insert_datas[] = [
                                        'facility_cd' => $datas[1]['facility_cd'], 'user_cd' => $datas[1]['user_cd'], 'start_date' => $datas[2]['start_date'], 'end_date' => $datas[2]['end_date'], 'main_flg' => 1, 'created_by' => 'I03-B03', 'created_at' => $this->date, 'updated_by' => 'I03-B03', 'updated_at' => $this->date
                                    ];
                                }
                            } else {
                                // Data invalid
                            }
                        }
                    } else if ($user_cd_position == 1) {
                        $insert_datas[] = [
                            'facility_cd' => $datas[1]['facility_cd'], 'user_cd' => $datas[1]['user_cd'], 'start_date' => $datas[1]['start_date'], 'end_date' => $datas[1]['end_date'], 'main_flg' => 1, 'created_by' => 'I03-B03', 'created_at' => $this->date, 'updated_by' => 'I03-B03', 'updated_at' => $this->date
                        ];
                        if ($datas[1]['start_date'] >= $datas[0]['end_date']) {
                            $datas[0]['end_date'] = date_create($datas[1]['start_date'])->modify('-1 days')->format('Y-m-d');
                            if ($datas[0]['end_date'] < $datas[0]['start_date']) {
                                // Data invalid
                            } else {
                                $insert_datas[] = [
                                    'facility_cd' => $datas[0]['facility_cd'], 'user_cd' => $datas[0]['user_cd'], 'start_date' => $datas[0]['start_date'], 'end_date' => $datas[0]['end_date'], 'main_flg' => 1, 'created_by' => 'I03-B03', 'created_at' => $this->date, 'updated_by' => 'I03-B03', 'updated_at' => $this->date
                                ];
                            }
                        } else {
                            if ($datas[1]['end_date'] < $datas[0]['end_date']) {
                                $datas[2]['start_date'] = date_create($datas[1]['end_date'])->modify('+1 days')->format('Y-m-d');
                                $datas[2]['end_date'] = $datas[0]['end_date'];
                                $datas[0]['end_date'] = date_create($datas[1]['start_date'])->modify('-1 days')->format('Y-m-d');
                                if ($datas[0]['end_date'] < $datas[0]['start_date']) {
                                    // Data invalid
                                } else {
                                    $insert_datas[] = [
                                        'facility_cd' => $datas[0]['facility_cd'], 'user_cd' => $datas[0]['user_cd'], 'start_date' => $datas[0]['start_date'], 'end_date' => $datas[0]['end_date'], 'main_flg' => 1, 'created_by' => 'I03-B03', 'created_at' => $this->date, 'updated_by' => 'I03-B03', 'updated_at' => $this->date
                                    ];
                                    $insert_datas[] = [
                                        'facility_cd' => $datas[0]['facility_cd'], 'user_cd' => $datas[0]['user_cd'], 'start_date' => $datas[2]['start_date'], 'end_date' => $datas[2]['end_date'], 'main_flg' => 1, 'created_by' => 'I03-B03', 'created_at' => $this->date, 'updated_by' => 'I03-B03', 'updated_at' => $this->date
                                    ];
                                }
                            } else {
                                // Data invalid
                            }
                        }
                    } else {
                        // Exception
                    }
                } else {
                    $insert_datas[] = [
                        'facility_cd' => $datas[0]['facility_cd'], 'user_cd' => $datas[0]['user_cd'], 'start_date' => $datas[0]['start_date'], 'end_date' => $datas[0]['end_date'], 'main_flg' => 1, 'created_by' => 'I03-B03', 'created_at' => $this->date, 'updated_by' => 'I03-B03', 'updated_at' => $this->date
                    ];
                }
            }
        }
        return $this->repo->updateMasterEquipmentPerson($insert_datas, $jobId, $keyBatchJob);
    }

    private function __checkDuplicate($conditions = [])
    {
        $data = [];
        foreach ($this->arr_check as $key => $masterEquipment) {
            if ($conditions['user_cd'] == $masterEquipment['user_cd']) {
                $facility_user = [
                    'facility_cd' => $masterEquipment['facility_cd'],
                    'user_cd' => $masterEquipment['user_cd'],
                    'start_date' => $masterEquipment['start_date'],
                    'end_date' => $masterEquipment['end_date']
                ];
                array_push($data, $facility_user);
                unset($this->arr_check[$key]);
            }
        }
        return $data;
    }

    /**
     * update again data table m_facility_user from table h_facility_user
     * 
     * @author huynh
     */
    public function changeDataUserFacilityMaster($date, $jobId, $keyBatchJob)
    {
        $this->repo->emptyTable('m_facility_user', $jobId, $keyBatchJob);
        $this->serviceAccessLog->logInfoContent($jobId, $keyBatchJob, "deleted successfully m_facility_user data");
        $this->serviceAccessLog->logInfoContent($jobId, $keyBatchJob, "Insserted successfully m_facility_user from m_facility_user data");
        $this->repo->updateUserFacilityMaster($date, $jobId, $keyBatchJob);
    }
}
