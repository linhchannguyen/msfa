<?php

namespace App\Services;

use App\Repositories\RecommendedPeriodConfirmation\RecommendedPeriodConfirmationRepositoryInterface;
use Carbon\Carbon as time;
use App\Services\SystemParameterService;
use App\Traits\DateTimeTrait;
use App\Traits\ArrayTrait;

class RecommendedPeriodConfirmationService
{
    use DateTimeTrait, ArrayTrait;
    private $repo, $serviceDate, $date;

    public function __construct(RecommendedPeriodConfirmationRepositoryInterface $repo, SystemParameterService $serviceDate)
    {
        $this->repo = $repo;
        $this->serviceDate = $serviceDate;
        $this->date = date_create($this->serviceDate->getCurrentSystemDateTime())->format('Y/m/d');
    }

    public function getData($request)
    {
        $user_name = $request->user_name;
        $user_cd = $request->user_cd;
        $date = $request->date ? $request->date : $this->date;
        $org_cd = $request->org_cd;
        $limit = $request->filled('limit') ? $request->limit : 100;
        $offset = $request->filled('offset') ? $request->offset : 0;
        $user_list = $this->repo->getDataUser($user_cd, $user_name, $org_cd, $date, $limit, $offset);
        $user_cd_list = array_column($user_list['records'], 'user_cd');
        if (count($user_list['records']) > 0) {
            $user_information = $this->repo->getUserInformation($user_cd_list, $user_name, $org_cd, $date);
            $organization_information = $this->repo->getOrganizationInformation($user_cd_list, $org_cd, $date);
            $role_information = $this->repo->getRoleInformation($user_cd_list, $date);
            $approval_daily_report = $this->repo->getApproval($user_cd_list, $date, "報告");
            $approval_briefing = $this->repo->getApproval($user_cd_list, $date, "説明会");
            $approval_lecture = $this->repo->getApproval($user_cd_list, $date, "講演会");
            $approval_knowledge = $this->repo->getApproval($user_cd_list, $date, "ナレッジ");

            foreach ($user_list['records'] as $user_item) {
                $user_item = (object)$user_item;
                $user_item->user_information = $user_item->organization_information = $user_item->role_information = [];
                $user_item->approval_daily_report = $user_item->approval_briefing = $user_item->approval_lecture = $user_item->approval_knowledge = [];
                $user_item->advance_reservation = !empty($user_item->advance_reservation) ? json_decode($user_item->advance_reservation) : [];
                array_multisort(array_column($user_item->advance_reservation, 'main_flag_as'),SORT_DESC,$user_item->advance_reservation);

                // user_information
                if (count((array)$user_information) > 0) {
                    $key = array_search($user_item->user_cd, array_column($user_information, 'user_cd'));
                    if ($key !== false) {
                        $user_item->user_information = !empty($user_information[$key]->user_information) ? json_decode($user_information[$key]->user_information) : [];
                    }
                }

                // organization_information
                if (count((array)$organization_information) > 0) {
                    $key_org = array_search($user_item->user_cd, array_column($organization_information, 'user_cd'));
                    if ($key_org !== false) {
                        $organization_information_temp = [];
                        foreach ($organization_information as $org_item) {
                            $org_item = (object)$org_item;
                            if ($user_item->user_cd == $org_item->user_cd) {
                                $organization = !empty($org_item->organization) ? json_decode($org_item->organization) : [];
                                array_multisort(array_column($organization, 'main_flag_as'),SORT_DESC,$organization);
                                $organization_information_temp[] = [
                                    'start_date' => $org_item->start_date,
                                    'end_date' => $org_item->end_date,
                                    'organization' => $organization
                                ];
                            }
                        }
                        $user_item->organization_information = $organization_information_temp;
                    }
                }

                // role_information
                if (count((array)$role_information) > 0) {
                    $key_role = array_search($user_item->user_cd, array_column($role_information, 'user_cd'));
                    if ($key_role !== false) {
                        $role_information_temp = [];
                        foreach ($role_information as $role_item) {
                            $role_item = (object)$role_item;
                            if ($user_item->user_cd == $role_item->user_cd) {
                                $role = !empty($role_item->role) ? json_decode($role_item->role) : [];
                                $role_information_temp[] = [
                                    'start_date' => $role_item->start_date,
                                    'end_date' => $role_item->end_date,
                                    'roles' => $role
                                ];
                            }
                        }
                        $user_item->role_information = $role_information_temp;
                    }
                }

                // approval_daily_report
                if (count((array)$approval_daily_report)) {
                    $key_daily_report = array_search($user_item->user_cd, array_column($approval_daily_report, 'request_user_cd'));
                    if ($key_daily_report !== false) {
                        $user_item->approval_daily_report = $this->formatApproval($approval_daily_report,$user_item->user_cd);
                    }
                }

                // approval_briefing
                if (count((array)$approval_briefing)) {
                    $key_briefing = array_search($user_item->user_cd, array_column($approval_briefing, 'request_user_cd'));
                    if ($key_briefing !== false) {
                        $user_item->approval_briefing = $this->formatApproval($approval_briefing,$user_item->user_cd);
                    }
                }
                
                // approval_lecture
                if (count((array)$approval_lecture)) {
                    $key_lecture = array_search($user_item->user_cd, array_column($approval_lecture, 'request_user_cd'));
                    if ($key_lecture !== false) {
                        $user_item->approval_lecture = $this->formatApproval($approval_lecture,$user_item->user_cd);
                    }
                }

                // approval_knowledge
                if (count((array)$approval_knowledge)) {
                    $key_knowledge = array_search($user_item->user_cd, array_column($approval_knowledge, 'request_user_cd'));
                    if ($key_knowledge !== false) {
                        $user_item->approval_knowledge = $this->formatApproval($approval_knowledge,$user_item->user_cd);
                    }
                }
            }
        }
        return $user_list;
    }

    public function formatApproval($array_format,$user_cd)
    {
        $array_temp = [];
        foreach ($array_format as $approval_user) {
            $approval_user = (object)$approval_user;
            $total = [];
            $approval_layer_num = [];
            if ($user_cd == $approval_user->request_user_cd) {
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

                $array_temp[] = [
                    'start_date' => $approval_user->start_date,
                    'end_date' => $approval_user->end_date,
                    'approval_layer_num' => $total
                ];
            }
        }
        return $array_temp;
    }
}
