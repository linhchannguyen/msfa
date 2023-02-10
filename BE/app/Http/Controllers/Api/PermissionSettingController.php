<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\PermissionSetting\DeletePermissionRequest;
use App\Http\Requests\Api\PermissionSetting\GetListPermissionSettingRequest;
use App\Http\Requests\Api\PermissionSetting\UpdatePermissionRequest;
use App\Services\PermissionSettingService;
use Exception;
use App\Traits\DateTimeTrait;
use Illuminate\Support\Facades\DB;
use App\Traits\RolePolicyTrait;
use App\Services\RolePolicyService;
use Illuminate\Http\Request;

class PermissionSettingController extends Controller
{
    use DateTimeTrait;
    use RolePolicyTrait;
    private $service;
    private $role;

    public function __construct(PermissionSettingService $service, RolePolicyService $role)
    {
        $this->service = $service;
        $this->role = $role;
        $this->middleware('role.has:' . config('role.MASTER_MG.CODE') . ',' . config('role.SYS_MG.CODE'));
    }

    /**
     *  @OA\Get(
     *      path="/api/permission-setting/get-screen-data",
     *      tags={"I01-S02"},
     *      summary="Permission Setting",
     *      description="Get Screen Data",
     *      security={{"jwt": {}}},
     *      @OA\Response(response=200, description="Response successfully",
     *             @OA\JsonContent(
     *              type="object",
     *              example={
     *                       "status": "200",
     *                       "message": "success",
     *                       "data": {
     *                           "role": {
     *                                      {
     *                                          "role": "R01",
     *                                          "role_short_name": "シ利",
     *                                          "role_name": "システム利用者",
     *                                          "memo": "本システムの利用者全員に付与されるロール。運用後に「この画面へは誰もアクセスさせたくない」といった要望があるため、1つのロールとして必要。"
     *                                      }
     *                           }
     *                       }
     *                   }
     *              )
     *        ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     *    )
     */
    public function getScreenData()
    {
        $data = $this->service->getScreenData();
        return $this->responseSuccess("success", $data);
    }

    /**
     *  @OA\Get(
     *      path="/api/permission-setting",
     *      tags={"I01-S02"},
     *      summary="Permission Setting",
     *      description="Get Permission List",
     *      security={{"jwt": {}}},
     *      @OA\Parameter(name="user_name",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="user_cd",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="org_cd",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="reference_date",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="director",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="limit",in="query", @OA\Schema(type="number")),
     *      @OA\Parameter(name="offset",in="query", @OA\Schema(type="number")),
     *      @OA\Response(response=200, description="Response successfully",
     *      @OA\JsonContent(
     *       type="object",
     *       example={
     *                "status": "200",
     *                "message": "success",
     *                "data": {
     *                    "records": {
     *                                   {
     *                                        "user_cd": "10001",
     *                                        "user_name": "西嶋 洋明",
     *                                        "organization": {
     *                                            {
     *                                                "org_cd": "10000",
     *                                                "org_label": "営業部門",
     *                                                "main_flag": "主勤務先",
     *                                                "definition_label": "営業部門長",
     *                                                "definition_value": "16"
     *                                            },
     *                                            {
     *                                                "org_cd": "21000",
     *                                                "org_label": "営業企画部",
     *                                                "main_flag": "主勤務先",
     *                                                "definition_label": "営業部門長",
     *                                                "definition_value": "16"
     *                                            }
     *                                        },
     *                                        "user_role": {
     *                                                  {
     *                                                      "start_date": "2018/01/02",
     *                                                      "end_date": "2019/01/04",
     *                                                      "role": {
     *                                                          {
     *                                                              "role": "R01",
     *                                                              "role_name": "システム利用者",
     *                                                              "role_short_name": "シ利"
     *                                                          },
     *                                                          {
     *                                                              "role": "R80",
     *                                                              "role_name": "マスタ管理者",
     *                                                              "role_short_name": "マ管"
     *                                                          }
     *                                                      }
     *                                                  }
     *                                              }
     *                                        }
     *                              },
     *                          "pagination": {
     *                              "total_items": 1,
     *                              "total_pages": 1,
     *                              "previous_page": 1,
     *                              "next_page": 1,
     *                              "current_page": 1,
     *                              "first_page": 1,
     *                              "last_page": 1
     *                          }
     *                   }
     *               }
     *          )
     *        ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     *    )
     */

    public function getPermissionList(Request $request)
    {
        $limit = $request->filled('limit') ? $request->limit : 100;
        $offset = $request->filled('offset') ? $request->offset : 0;
        $data = $this->service->getPermissionList($request->user_name, $request->user_cd, $request->org_cd, $request->reference_date, $request->director, $limit, $offset);
        return $this->responseSuccess("success", $data);
    }

    /**
     * @OA\POST(
     *      path="/api/permission-setting/upsert-permission",
     *      operationId="upsertPermission",
     *      tags={"I01-S02"},
     *      summary="Update Permission",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\RequestBody(
     *          description="Update User Permission",
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(
     *                      property="user_cd",
     *                      type="string",
     *                      description="user cd",
     *                      default="10001"
     *                  ),
     *                  @OA\Property(
     *                      property="user_role",
     *                      type="string",
     *                      description="array user role",
     *                      default="[]"
     *                  )
     *              ),
     *          ),
     *      ),
     *      @OA\Response(response=201, description="Successful response"),
     *      @OA\Response(response=403, description="Forbidden Response")
     * )
     */
    public function upsertPermission(UpdatePermissionRequest $request)
    {
        /**
         * If the user array that has ver after the modified date is not sorted by date, it will get an error
         */
        DB::beginTransaction();
        try {
            $current = $this->currentJapanDateTime('Y-m-d');
            $user_role = (!is_array($request->user_role) || empty($request->user_role)) ? [] : $request->user_role;
            if (!$this->validateRole($user_role)) {
                return $this->responseErrorValidate('ロールを入力してください。', ["user_role" => ["ユーザーロールを入力してください。"]]);
            }
            if (!empty($user_role)) {
                $datas = [];
                $user_cds = explode(",", trim($request->user_cd));
                $start_date_olds = explode(",", trim($request->start_date_old));
                $lastRoles = [];
                $user_unpermission = [];
                foreach ($user_role as &$conv_data) {
                    $conv_data['start_date'] = date('Y-m-d', strtotime($conv_data['start_date']));
                }
                foreach ($user_cds as $index => $user_cd) {
                    $role_quantity = 0;
                    if ($request->create_flag === 1 || $request->create_flag === "1") {
                        $roles = $this->service->getRoleList([$user_cd], $current);
                        $user_role = $this->_arrayMerge($roles);
                        $lastRoles = (count($user_role) > 0) ? explode(',', $user_role[count($user_role) - 1]['roles']) : [];
                        $request_user_role_tmp = $request->user_role[0];
                        unset($request_user_role_tmp['start_date']);
                        $request_user_role_tmp['start_date'] = date('Y-m-d', strtotime($request->user_role[0]['start_date']));
                        if ($current == $request_user_role_tmp['start_date'] || count($user_cds) > 1) {
                            $index_change = array_search($request_user_role_tmp['start_date'], array_column($user_role, 'start_date'));
                            if ($index_change !== false) {
                                $user_role[$index_change] = $request_user_role_tmp;
                            } else {
                                array_push($user_role, $request_user_role_tmp);
                            }
                        } else {
                            array_push($user_role, $request_user_role_tmp);
                        }
                    }
                    $upsert_current_date_flag = false;
                    if (count($user_role) > 1) {
                        $index_change = array_search(1, array_column($user_role, 'change_flag'));
                        if ($index_change !== false) {
                            $start_date_change = $user_role[$index_change]['start_date'];
                            $end_date_change = $user_role[$index_change]['end_date'];
                            $start_date_change = $this->responseDateTimeFormat($start_date_change, 'Y-m-d');
                            $quantity_date_change = array_count_values(array_column($user_role, 'start_date'))[$start_date_change];
                            if ($quantity_date_change > 1) {
                                if ($current != $start_date_change) {
                                    if (count($user_cds) == 1) { //Single mode
                                        return $this->responseErrorValidate(__('permission.by_date', ['attribute' => date_create($start_date_change)->format('Y/m/d')]));
                                    }
                                } else {
                                    $upsert_current_date_flag = true;
                                }
                            }
                            if ($start_date_change < $current) { //Start_date change date < system date, error is reported
                                return $this->responseErrorValidate(__('permission.system_date', ['attribute' => date_create($current)->format('Y/m/d')]));
                            }
                            array_multisort(
                                array_column($user_role, 'start_date'),
                                SORT_ASC,
                                $user_role
                            );
                            if ($upsert_current_date_flag && !isset($request->create_flag)) {
                                foreach ($user_role as $i => $value) {
                                    if ($value['start_date'] >= $current && $value['end_date'] <= $end_date_change) {
                                        unset($user_role[$i]);
                                        if ($value['change_flag']) {
                                            $user_role[$i] = $value;
                                        } else {
                                            $this->service->deletePermission($user_cd, $value['start_date']);
                                        }
                                    }
                                }
                            }
                            $user_role = array_values($user_role);
                            array_multisort(
                                array_column($user_role, 'start_date'),
                                SORT_ASC,
                                $user_role
                            );
                            foreach ($user_role as $i => $value) {
                                if ($i > 0) {
                                    $user_role[$i - 1]['end_date'] = date_create($value['start_date'])->modify('-1 days')->format('Y-m-d');
                                }
                                if ($i = count($user_role) - 1) {
                                    $user_role[$i]['end_date'] = '9999-12-31';
                                }
                            }
                        } else {
                            return $this->responseErrorValidate(__('permission.change_flag', ['attribute' => 'change_flag']));
                        }
                    } else {
                        $start_date_change = $this->responseDateTimeFormat($user_role[0]['start_date'], 'Y-m-d');
                        if ($start_date_change < $current) { //Start_date change date < system date, error is reported
                            return $this->responseErrorValidate(__('permission.system_date', ['attribute' => date_create($current)->format('Y/m/d')]));
                        }
                        $user_role[0]['end_date'] = '9999-12-31';
                    }
                    if (count($user_cds) > 1) { //Multi mode
                        foreach ($user_role as $i => $value) {
                            if ($value['start_date'] > $request->user_role[0]['start_date'] && $value['start_date'] <= $start_date_olds[$index]) {
                                $this->service->deletePermission($user_cd, $value['start_date']);
                                unset($user_role[$i]);
                            }
                        }
                        $user_role = array_values($user_role);
                        foreach ($user_role as $i => $value) {
                            if ($i > 0) {
                                $user_role[$i - 1]['end_date'] = date_create($value['start_date'])->modify('-1 days')->format('Y-m-d');
                            }
                            if ($i = count($user_role) - 1) {
                                $user_role[$i]['end_date'] = '9999-12-31';
                            }
                        }
                    }
                    //Sort again to delete the old data from the first ver of the array, otherwise sorting may cause duplicate sql errors
                    array_multisort(
                        array_column($user_role, 'start_date'),
                        SORT_ASC,
                        $user_role
                    );
                    foreach ($user_role as $key => $value) {
                        if ($value['start_date'] > $value['end_date']) { //Check the processed array is correct (start must be <= end)
                            return $this->responseSystemError(__('permission.system_error'));
                        }
                        if ($key < (count($user_role) - 1)) {
                            if ($value['start_date'] > $user_role[$key + 1]['start_date'] || $value['end_date'] > $user_role[$key + 1]['start_date']) {
                                return $this->responseSystemError(__('permission.system_error'));
                            }
                        }
                        $roles = explode(",", $value['roles']);
                        $master_user_role_insert = [];
                        foreach ($roles as $role) {
                            if (substr($role, -1) == '-') {
                                $role = str_replace('-', '', $role);
                                if (!in_array($role, $lastRoles)) {
                                    $role = "";
                                }
                            }
                            if ($role != "") {
                                array_push($master_user_role_insert, ['user_cd' => $user_cd,  'role' => $role]);
                                $role_quantity++;
                                $arr_user_role = [
                                    "user_cd" => $user_cd,
                                    "role" => $role ?? "",
                                    "start_date" => $value['start_date'],
                                    "end_date" => $value['end_date']
                                ];
                                if (isset($value['start_date_old'])) {
                                    $arr_user_role['start_date_old'] = $value['start_date_old'];
                                }
                                array_push($datas, $arr_user_role);
                            }
                        }
                        if ($value['start_date'] == $current && !empty($master_user_role_insert)) {
                            $this->service->updateMasterUserRole($master_user_role_insert);
                        }
                    }
                    if ($role_quantity == 0) {
                        array_push($user_unpermission, $user_cd);
                    }
                }
                //Check the list of unauthorized users
                if (!empty($user_unpermission)) {
                    return $this->responseErrorValidate('unpermission_list', $user_unpermission);
                }
                $request->current_date = $current;
                $request->datas = $datas;
                $this->service->updatePermission($request);
            }
            DB::commit();
            return $this->responseCreated(__('permission.save_successfully'));
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
            return $this->responseSystemError(__('permission.system_error'));
        }
    }

    protected function _arrayMerge($arr)
    {
        $result = [];
        foreach ($arr as $data) {
            $array_date = array_unique(array_column(json_decode($data->user_role), 'start_date'));
            $user_roles = json_decode($data->user_role);
            foreach ($array_date as $start_date) {
                $user_role = [];
                $role = "";
                $end_date = "";
                foreach ($user_roles as $value1) {
                    if ($start_date == $value1->start_date) {
                        $end_date = $value1->end_date;
                        $role .= "$value1->role,";
                    }
                }
                $role = rtrim($role, ",");
                $user_role['start_date'] = $start_date;
                $user_role['end_date'] = $end_date;
                $user_role['roles'] = $role;
                $user_role['change_flag'] = 0;
                $result[] = $user_role;
            }
        }
        array_multisort(
            array_column($result, 'start_date'),
            SORT_ASC,
            $result
        );
        return $result;
    }

    public function validateRole($arr)
    {
        foreach ($arr as $value) {
            if (empty($value['roles'])) {
                return false;
            }
        }
        return true;
    }

    /**
     *  @OA\Delete(
     *      path="/api/permission-setting/delete-permission",
     *      tags={"I01-S02"},
     *      summary="Permission Setting",
     *      description="Delete Permission Setting",
     *      security={{"jwt": {}}},
     *      @OA\RequestBody(
     *          description="Delete Permission Setting params",
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(
     *                      property="user_cd",
     *                      type="string",
     *                      description="The user code",
     *                      default="tutx"
     *                  ),
     *                  @OA\Property(
     *                      property="start_date",
     *                      type="date",
     *                      description="The start date",
     *                      default="2021-11-10"
     *                  )
     *              ),
     *          ),
     *      ),
     *      @OA\Response(response=301, description="Delete successfully",
     *             @OA\JsonContent(
     *              type="object",
     *              example={
     *                       "status": "204",
     *                       "message": "正常に削除しました。",
     *                       "data": {}
     *                   }
     *              )
     *        ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     *    )
     */
    public function deletePermission(DeletePermissionRequest $request)
    {
        DB::beginTransaction();
        try {
            $dataPermission = $this->service->getCurrenntPermission($request->user_cd, $request->start_date);
            if (count((array)$dataPermission) > 0) {
                $this->service->updatePermissionEndDate($request->user_cd, $dataPermission[0]->start_date, $request->end_date);
            }
            $this->service->deletePermission($request->user_cd, $request->start_date);
            DB::commit();
            return $this->responseNoContent(__('permission.delete'));
        } catch (Exception $e) {
            DB::rollBack();
            //Error
            throw $e;
            return $this->responseSystemError(__('permission.system_error'));
        }
    }
}
