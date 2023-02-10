<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\UserManagement\CreateUserRequest;
use App\Http\Requests\Api\UserManagement\DeleteApprovalUserRequest;
use App\Http\Requests\Api\UserManagement\DeleteUserOrganizationRequest;
use App\Http\Requests\Api\UserManagement\DeleteUserRequest;
use App\Http\Requests\Api\UserManagement\UpdateApprovalUserRequest;
use App\Http\Requests\Api\UserManagement\UpdateUserOrganizationRequest;
use App\Http\Requests\Api\UserManagement\UpdateUserRequest;
use App\Http\Requests\Api\UserManagement\UserListRequest;
use App\Services\SystemParameterService;
use App\Services\UserManagementService;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;
use App\Traits\DateTimeTrait;
use App\Traits\StringConvertTrait;
use App\Jobs\SendEmail;
use App\Services\PasswordReissueService;
use App\Mail\PasswordCreateUserMail;

class UserManagementController extends Controller
{
    use DateTimeTrait;
    use StringConvertTrait;
    private $userManagement, $pass, $systemParameterService;

    public function __construct(UserManagementService $userManagement, SystemParameterService $systemParameterService, PasswordReissueService $pass)
    {
        $this->userManagement = $userManagement;
        $this->systemParameterService = $systemParameterService;
        $this->pass = $pass;
        $this->middleware('role.has:' . config('role.MASTER_MG.CODE') . ',' . config('role.SYS_MG.CODE'));
    }

    /**
     *  @OA\GET(
     *      path="/api/user-management/get-screen-data",
     *      operationId="getScreenDataUserManagement",
     *      tags={"I01-S01"},
     *      summary="User Management",
     *      description="Get Screen Data",
     *      security={{"jwt": {}}},
     *      @OA\Response(response=200, description="Response successfully",
     *             @OA\JsonContent(
     *              type="object",
     *              example={
     *                      "status": "200",
     *                      "message": "success",
     *                      "data": {
     *                          "user_post_type": {
     *                                  {
     *                                      "definition_label": "MR",
     *                                      "definition_value": "10"
     *                                  }
     *                              },
     *                          "approval_title": {
     *                              {
     *                                  "definition_label": "報告",
     *                                  "definition_value": "1"
     *                              }
     *                          },
     *                          "approval_layer_num": {
     *                              {
     *                                  "parameter_key": "ナレッジ",
     *                                  "parameter_value": "2"
     *                              }
     *                          }
     *                      }
     *                  }
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
        $result = $this->userManagement->getScreenData();
        return $this->responseSuccess("success", $result);
    }

    /**
     *  @OA\GET(
     *      path="/api/user-management/user-list",
     *      operationId="userList",
     *      tags={"I01-S01"},
     *      summary="User Management",
     *      description="User Management",
     *      security={{"jwt": {}}},
     *      @OA\Parameter(name="user_cd",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="user_name",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="org_cd",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="limit",in="query", @OA\Schema(type="number")),
     *      @OA\Parameter(name="offset",in="query", @OA\Schema(type="number")),
     *      @OA\Response(response=200, description="Response successfully",
     *             @OA\JsonContent(
     *              type="object",
     *              example={
     *                      "status": "200",
     *                      "message": "success",
     *                      "data": {
     *                          "records": {
     *                              {
     *                                  "user_cd": "10001",
     *                                  "start_date": "2019-04-01",
     *                                  "end_date": "2021-11-16",
     *                                  "user_name": "西嶋 洋明",
     *                                  "mail_address": "10001@dummy.ne.jp",
     *                                  "account_lock_flag": 0,
     *                                  "account_lock_remarks": null,
     *                                  "advance_reservation": {
     *                                      {
     *                                          "user_cd": "10001",
     *                                          "start_date": "2021-11-17",
     *                                          "end_date": "2022-01-07",
     *                                          "user_name": "西嶋 洋明",
     *                                          "mail_address": "10001@dummy.ne.jp",
     *                                          "account_lock_flag": null,
     *                                          "account_lock_remarks": null
     *                                      }
     *                                  }
     *                              }
     *                          },
     *                          "pagination": {
     *                              "total_items": 1,
     *                              "total_pages": 1,
     *                              "previous_page": 1,
     *                              "next_page": 1,
     *                              "current_page": 1,
     *                              "first_page": 1,
     *                              "last_page": 1
     *                          }
     *                      }
     *                  }
     *              )
     *        ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     *    )
     */

    public function userList(Request $request)
    {
        $limit = $request->filled('limit') ? $request->limit : 100;
        $offset = $request->filled('offset') ? $request->offset : 0;
        $request->user_name = $this->convert_kana($request->user_name);
        $data = $this->userManagement->getUserList($request->user_cd, $request->user_name, $request->org_cd, $limit, $offset);
        return $this->responseSuccess("success", $data);
    }

    /**
     *  @OA\POST(
     *      path="/api/user-management/create-user",
     *      operationId="createUser",
     *      tags={"I01-S01"},
     *      summary="User Management",
     *      description="Create User",
     *      security={{"jwt": {}}},
     *      @OA\RequestBody(
     *          description="Create User params",
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
     *                      property="user_name",
     *                      type="string",
     *                      description="The user name",
     *                      default="TuanTA"
     *                  ),
     *                  @OA\Property(
     *                      property="mail_address",
     *                      type="string",
     *                      description="The mail address",
     *                      default="anc@gmail.com"
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
     *      @OA\Response(response=201, description="Saved successfully",
     *             @OA\JsonContent(
     *              type="object",
     *              example={
     *                       "status": "201",
     *                       "message": "正常に保存しました。",
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

    public function createUser(CreateUserRequest $request)
    {
        DB::beginTransaction();
        try {
            $date_system = date_create($this->systemParameterService->getCurrentSystemDateTime())->format('Y-m-d');
            $request->start_date = $this->responseDateTimeFormat($request->start_date, 'Y-m-d');
            $user_exist = $this->userManagement->checkUserExist($request->user_cd, '');
            if (count($user_exist) > 0) {
                return $this->responseErrorValidate(__('usermanagement.user_exist'));
            }
            $this->userManagement->createUser($request->user_cd, $this->convert_kana($request->user_name), $request->mail_address, $request->start_date, '9999-12-31', 0, '');
            if($date_system == $request->start_date){
                $this->userManagement->updateMasterUser($request->user_cd, $this->convert_kana($request->user_name), $request->mail_address);
            }
            // create password
            $generate_pass = $this->generate_pass();
            $passphrase = bcrypt($generate_pass);
            $this->userManagement->createPassphrase($request->user_cd, $passphrase);

            // send mail user 
            $userInfo = $this->pass->getInfoSendEmail($request->user_cd);
            $system_name = count((array)$userInfo['email']) > 0 ? $userInfo['email'][0]->parameter_value : '';
            $mail_to = count((array)$userInfo['mailto']) > 0 ? $userInfo['mailto'][0]->parameter_value : '';

            $data = [
                'title' => "【" . $system_name . "】ユーザ登録完了のお知らせ",
                'system_name' => $system_name,
                'passphrase' => $generate_pass,
                'user_name' => $request->user_name,
                'mail_to' => $mail_to,
                'start_date' => date_format(date_create($request->start_date), 'Y/m/d'),
                'url' =>  config('app.url_fe'),
                'login_id' => $request->user_cd,
                'email_contact' => $mail_to
            ];

            $mailable = new PasswordCreateUserMail($data);
            SendEmail::dispatchNow($request->mail_address, $mailable);

            DB::commit();
            return $this->responseCreated(__('usermanagement.save_create'));
        } catch (Exception $e) {
            //Error
            DB::rollBack();
            throw $e;
            return $this->responseSystemError(__('usermanagement.system_error'));
        }
    }

    public function checkForCharacterCondition($string)
    {
        return (bool) preg_match('/^(?=.*[0-9])(?=.*[a-z])(?=.*[a-zA-Z])[\x20-\x7E]{8,25}$/', $string);
    }

    public function generate_pass()
    {
        global $j;
        $allowedCharacters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890!@#$%^&*';
        $pass = '';
        $length = 10;
        $max = strlen($allowedCharacters) - 1;
        for ($i = 0; $i < $length; ++$i) {
            $pass .= $allowedCharacters[random_int(0, $max)];
        }

        if ($this->checkForCharacterCondition($pass)) {
            return $pass;
        } else {
            $j++;
            return $this->generate_pass();
        }
    }


    /**
     *  @OA\POST(
     *      path="/api/user-management/update-user",
     *      operationId="updateUser",
     *      tags={"I01-S01"},
     *      summary="User Management",
     *      description="Update User",
     *      security={{"jwt": {}}},
     *      @OA\RequestBody(
     *          description="Update User params",
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(
     *                      property="user_cd",
     *                      type="string",
     *                      description="The user code",
     *                      default="10001"
     *                  ),
     *                  @OA\Property(
     *                      property="start_date",
     *                      type="date",
     *                      description="Start Date",
     *                      default="2023-01-01"
     *                  ),
     *                  @OA\Property(
     *                      property="end_date",
     *                      type="date",
     *                      description="end Date",
     *                      default="2023-01-01"
     *                  ),
     *                  @OA\Property(
     *                      property="start_date_old",
     *                      type="date",
     *                      description="Start Date Old",
     *                      default="2023-01-01"
     *                  ),
     *                  @OA\Property(
     *                      property="user_name",
     *                      type="string",
     *                      description="The User Name",
     *                      default="abc"
     *                  ),
     *                  @OA\Property(
     *                      property="mail_address",
     *                      type="sting",
     *                      description="Mail Address",
     *                      default="anc@gmail.com"
     *                  ),
     *                  @OA\Property(
     *                      property="account_lock_flag",
     *                      type="number",
     *                      description="Account Lock Flag",
     *                      default="1"
     *                  ),
     *                  @OA\Property(
     *                      property="account_lock_remarks",
     *                      type="string",
     *                      description="Account Lock Remarks",
     *                      default="buxxxx"
     *                  ),
     *                  @OA\Property(
     *                      property="flag_change",
     *                      type="number",
     *                      description="change flag : 1 : edit , 0 : create",
     *                      default="1"
     *                  )
     *              ),
     *          ),
     *      ),
     *      @OA\Response(response=201, description="Saved successfully",
     *             @OA\JsonContent(
     *              type="object",
     *              example={
     *                       "status": "201",
     *                       "message": "正常に保存しました。",
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
    public function updateUser(UpdateUserRequest $request)
    {
        DB::beginTransaction();
        try {
            $request->start_date = $this->responseDateTimeFormat($request->start_date, 'Y-m-d');
            $request->start_date_old = $this->responseDateTimeFormat($request->start_date_old, 'Y-m-d');
            $request->end_date = $this->responseDateTimeFormat($request->end_date, 'Y-m-d');
            $date_system = date_create($this->systemParameterService->getCurrentSystemDateTime())->format('Y-m-d');
            $dateUser = $this->userManagement->getDateUser($request->user_cd, $date_system);

            if ($request->account_lock_flag == 1 && empty($request->account_lock_remarks)) {
                $data['account_lock_remarks'] = [__('usermanagement.account_lock_remarks', ['attribute' => 'アカウントロック理由'])];
                return $this->responseErrorValidate(__('usermanagement.account_lock_remarks', ['attribute' => 'アカウントロック理由']), $data);
            }

            if (!in_array($request->start_date_old, array_column($dateUser, 'start_date'))) {
                $dateUser[count($dateUser)] = (object)['start_date' => date_create($request->start_date)->format('Y-m-d'), 'end_date' => '9999-12-31'];
            } else {
                if ($request->flag_change == 1) {
                    $index_date_current = array_search($request->start_date_old, array_column($dateUser, 'start_date'));
                    $dateUser[$index_date_current]->start_date = $request->start_date;
                } else {
                    $dateUser[count($dateUser)] = (object)['start_date' => date_create($request->start_date)->format('Y-m-d'), 'end_date' => '9999-12-31'];
                }
            }

            $index_change = array_search($request->start_date, array_column($dateUser, 'start_date'));
            $upsert_current_date_flag = false;
            if ($index_change !== false) {
                $start_date_change = $dateUser[$index_change]->start_date;
                $quantity_date_change = array_count_values(array_column($dateUser, 'start_date'))[$start_date_change];
                if ($quantity_date_change > 1) {
                    if ($date_system != $start_date_change) {
                        return $this->responseErrorValidate(__('usermanagement.by_date', ['attribute' => date_create($start_date_change)->format('Y/m/d')]));
                    } else {
                        $upsert_current_date_flag = true;;
                    }
                }
                if ($request->start_date < $date_system) {
                    return $this->responseErrorValidate(__('usermanagement.system_date', ['attribute' => date_create($date_system)->format('Y/m/d')]));
                }
            }
            if (count((array)$dateUser) > 0) {
                foreach ($dateUser as $key => $value) {
                    // create variables start_date_old
                    $value = (object)$value;
                    if ($upsert_current_date_flag && $key == $index_change) {
                        $value->start_date_old = $request->start_date_old;
                        $value->upsert_flag = true;
                    } else {
                        $value->start_date_old = $value->start_date;
                        $value->upsert_flag = false;
                    }
                    if ($request->start_date_old == $value->start_date) {
                        $value->start_date = $request->start_date;
                    }
                }
                // sort start date in array
                array_multisort(
                    array_column($dateUser, 'start_date'),
                    SORT_ASC,
                    $dateUser
                );

                // edit end date all
                foreach ($dateUser as $i => $value) {
                    $value = (object)$value;
                    if ($i > 0) {
                        $dateUser[$i - 1]->end_date = date_create($value->start_date)->modify('-1 days')->format('Y-m-d');
                    }
                    if ($i == count($dateUser) - 1) {
                        $dateUser[$i]->end_date = '9999-12-31';
                    }
                }
                // update or insert current user with start date old param
                $current_user = array_search($request->start_date_old, array_column($dateUser, 'start_date'));
                if ($upsert_current_date_flag) {
                    $this->userManagement->deleteUser($request->user_cd, $request->start_date_old);
                    $upsertCurrentUser = $this->userManagement->checkUserExist($request->user_cd, $request->start_date);
                    if (count((array)$upsertCurrentUser) > 0) {
                        $userCurrent = $upsertCurrentUser[0];
                        $this->userManagement->updateUser($request->user_cd, $request->start_date, $request->start_date, $dateUser[$current_user]->end_date ?? $request->end_date, $this->convert_kana($request->user_name), $request->mail_address, $request->account_lock_flag, $request->account_lock_remarks);
                    } else {
                        $this->userManagement->createUser($request->user_cd, $this->convert_kana($request->user_name), $request->mail_address, $request->start_date, $dateUser[$current_user]->end_date ?? $request->end_date, $request->account_lock_flag, $request->account_lock_remarks);
                    }
                } else {
                    $dataCurrentUser = $this->userManagement->checkUserExist($request->user_cd, $request->start_date_old);
                    if (count((array)$dataCurrentUser) > 0) {
                        $userCurrent = $dataCurrentUser[0];
                        if ($userCurrent->user_cd == $request->user_cd && $userCurrent->user_name == $this->convert_kana($request->user_name) && $userCurrent->start_date == $request->start_date && $userCurrent->mail_address == $request->mail_address && $userCurrent->account_lock_flag == $request->account_lock_flag && $userCurrent->account_lock_remarks == $request->account_lock_remarks) {
                            return $this->responseErrorValidate(__('usermanagement.change_item'));
                        }
                        $this->userManagement->updateUser($request->user_cd, $request->start_date_old, $request->start_date, $dateUser[$current_user]->end_date ?? $request->end_date, $this->convert_kana($request->user_name), $request->mail_address, $request->account_lock_flag, $request->account_lock_remarks);
                    } else {
                        $this->userManagement->createUser($request->user_cd, $this->convert_kana($request->user_name), $request->mail_address, $request->start_date, $dateUser[$current_user]->end_date ?? $request->end_date, $request->account_lock_flag, $request->account_lock_remarks);
                    }
                }
                if($date_system == $request->start_date){
                    $this->userManagement->updateMasterUser($request->user_cd, $this->convert_kana($request->user_name), $request->mail_address);
                }
                // update end date all of user
                $edit_mode_flag = false;
                $upsertFinal = [];
                foreach ($dateUser as $i => $value) {
                    $value = (object)$value;
                    if ($value->upsert_flag) {
                        continue;
                    }
                    if (count($dateUser) == 2) { //create version
                        if (!$upsert_current_date_flag) {
                            $end_date = $value->end_date;
                        } else {
                            $end_date = '9999-12-31';
                        }
                    } else { //update version
                        $edit_mode_flag = true;
                        $end_date = $value->end_date;
                    }
                    $this->userManagement->updateEndDate($request->user_cd, $value->start_date, $end_date);
                    if ($edit_mode_flag && $upsert_current_date_flag) {
                        if ($value->start_date > $date_system && $value->end_date <= $request->end_date && $request->flag_change) {
                            $this->userManagement->deleteUser($request->user_cd, $value->start_date);
                        } else {
                            array_push($upsertFinal, $value);
                        }
                    }
                }
                //upsert final
                foreach ($upsertFinal as $i => $value) {
                    $value = (object)$value;
                    if ($i > 0) {
                        $upsertFinal[$i - 1]->end_date = date_create($value->start_date)->modify('-1 days')->format('Y-m-d');
                    }
                    if ($i == count($upsertFinal) - 1) {
                        $upsertFinal[$i]->end_date = '9999-12-31';
                    }
                }
                foreach ($upsertFinal as $i => $value) {
                    $value = (object)$value;
                    $this->userManagement->updateEndDate($request->user_cd, $value->start_date, $value->end_date);
                }
                //end upsert final
            }

            DB::commit();
            return $this->responseCreated(__('usermanagement.update'));
        } catch (Exception $e) {
            //Error
            DB::rollBack();
            throw $e;
            return $this->responseSystemError(__('usermanagement.system_error'));
        }
    }

    /**
     *  @OA\Delete(
     *      path="/api/user-management/delete-user",
     *      operationId="deleteUser",
     *      tags={"I01-S01"},
     *      summary="User Management",
     *      description="Delete User",
     *      security={{"jwt": {}}},
     *      @OA\RequestBody(
     *          description="Delete User params",
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
     *      @OA\Response(response=204, description="Delete successfully",
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
    public function deleteUser(DeleteUserRequest $request)
    {
        DB::beginTransaction();
        try {
            $dataUser = $this->userManagement->getUserUpdate($request->user_cd, $request->start_date);
            if (count((array)$dataUser) > 0) {
                $this->userManagement->updateEndDate($request->user_cd, $dataUser[0]->start_date, $request->end_date);
            }
            $this->userManagement->deleteUser($request->user_cd, $request->start_date);
            DB::commit();
            return $this->responseNoContent(__('usermanagement.delete'));
        } catch (Exception $e) {
            //Error
            DB::rollBack();
            throw $e;
            return $this->responseSystemError(__('usermanagement.system_error'));
        }
    }

    /**
     *  @OA\GET(
     *      path="/api/user-management/user-organization",
     *      operationId="userOrganization",
     *      tags={"I01-S01"},
     *      summary="User Management",
     *      description="User Organization",
     *      security={{"jwt": {}}},
     *      @OA\Parameter(name="user_cd",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="user_name",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="org_cd",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="limit",in="query", @OA\Schema(type="number")),
     *      @OA\Parameter(name="offset",in="query", @OA\Schema(type="number")),
     *      @OA\Response(response=200, description="Response successfully",
     *      @OA\JsonContent(
     *       type="object",
     *       example={
     *                   "status": "200",
     *                   "message": "success",
     *                   "data": {
     *                       "records": {
     *                           {
     *                               "user_cd": "10001",
     *                               "user_name": "西嶋 洋明",
     *                               "active": {
     *                                              {
     *                                                  "start_date": "2019/04/01",
     *                                                  "end_date": "2021/11/30",
     *                                                  "organization": {
     *                                                      {
     *                                                          "org_cd": "10000",
     *                                                          "definition_label": "営業部門長",
     *                                                          "org_label": "営業部門",
     *                                                          "main_flag": 1,
     *                                                          "user_post_type": "16"
     *                                                      }
     *                                                  }
     *                                              }
     *                                          },
     *                                "advance_reservation": {
     *                                              {
     *                                                  "start_date": "2021/12/01",
     *                                                  "end_date": "2022/12/01",
     *                                                  "organization": {
     *                                                      {
     *                                                          "org_cd": "10000",
     *                                                          "definition_label": "営業部門長",
     *                                                          "org_label": "営業部門",
     *                                                          "main_flag": 1,
     *                                                          "user_post_type": "16"
     *                                                      }
     *                                                  }
     *                                              }
     *                                          }
     *                              }
     *                       },
     *                       "pagination": {
     *                           "total_items": 1,
     *                           "total_pages": 1,
     *                           "previous_page": 1,
     *                           "next_page": 1,
     *                           "current_page": 1,
     *                           "first_page": 1,
     *                           "last_page": 1
     *                       }
     *                   }
     *               }
     *             )
     *        ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     *    )
     */

    public function getListUserOrganization(Request $request)
    {
        $limit = $request->filled('limit') ? $request->limit : 100;
        $offset = $request->filled('offset') ? $request->offset : 0;
        $request->user_name = $this->convert_kana($request->user_name);
        $data = $this->userManagement->getListUserOrganization($request->user_cd, $request->user_name, $request->org_cd, $limit, $offset);
        return $this->responseSuccess("success", $data);
    }

    /**
     *  @OA\POST(
     *      path="/api/user-management/update-user-organization",
     *      operationId="updateUserOrganization",
     *      tags={"I01-S01"},
     *      summary="User Management",
     *      description="Update User Organization",
     *      security={{"jwt": {}}},
     *      @OA\RequestBody(
     *          description="Update User Organization params",
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(
     *                      property="user_cd",
     *                      type="string",
     *                      description="The user code",
     *                      default="10001"
     *                  ),
     *                  @OA\Property(
     *                      property="start_date",
     *                      type="date",
     *                      description="Start Date",
     *                      default="2023-01-01"
     *                  ),
     *                  @OA\Property(
     *                      property="end_date",
     *                      type="date",
     *                      description="end Date",
     *                      default="2023-01-01"
     *                  ),
     *                  @OA\Property(
     *                      property="start_date_old",
     *                      type="date",
     *                      description="Start Date Old",
     *                      default="2023-01-01"
     *                  ),
     *                  @OA\Property(
     *                      property="flag_change",
     *                      type="number",
     *                      description="change flag",
     *                      default="1"
     *                  ),
     *                  @OA\Property(
     *                      property="organization",
     *                      type="string",
     *                      description="User Organization",
     *                      default="[]"
     *                  )
     *              ),
     *          ),
     *      ),
     *      @OA\Response(response=201, description="Saved successfully",
     *             @OA\JsonContent(
     *              type="object",
     *              example={
     *                       "status": "201",
     *                       "message": "正常に保存しました。",
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
    public function updateUserOrganization(UpdateUserOrganizationRequest $request)
    {
        DB::beginTransaction();
        try {
            $request->start_date = $this->responseDateTimeFormat($request->start_date, 'Y-m-d');
            $request->start_date_old = $this->responseDateTimeFormat($request->start_date_old, 'Y-m-d');
            $request->end_date = $this->responseDateTimeFormat($request->end_date, 'Y-m-d');

            $date_system = date_create($this->systemParameterService->getCurrentSystemDateTime())->format('Y-m-d');
            $dateUserOrg = $this->userManagement->getDateUserOrg($request->user_cd, $date_system);

            if (!in_array($request->start_date_old, array_column($dateUserOrg, 'start_date'))) {
                $dateUserOrg[count($dateUserOrg)] = (object)['start_date' => date_create($request->start_date)->format('Y-m-d'), 'end_date' => '9999-12-31'];
            } else {
                if ($request->flag_change == 1) {
                    $index_date_current = array_search($request->start_date_old, array_column($dateUserOrg, 'start_date'));
                    $dateUserOrg[$index_date_current]->start_date = $request->start_date;
                } else {
                    $dateUserOrg[count($dateUserOrg)] = (object)['start_date' => date_create($request->start_date)->format('Y-m-d'), 'end_date' => '9999-12-31'];
                }
            }

            $index_change = array_search($request->start_date, array_column($dateUserOrg, 'start_date'));
            $upsert_current_date_flag = false;
            if ($index_change !== false) {
                $start_date_change = $dateUserOrg[$index_change]->start_date;
                $quantity_date_change = array_count_values(array_column($dateUserOrg, 'start_date'))[$start_date_change];
                if ($quantity_date_change > 1) {
                    if ($date_system != $start_date_change) {
                        return $this->responseErrorValidate(__('usermanagement.by_date', ['attribute' => date_create($start_date_change)->format('Y/m/d')]));
                    } else {
                        $upsert_current_date_flag = true;
                    }
                }
                if ($request->start_date < $date_system) {
                    return $this->responseErrorValidate(__('usermanagement.system_date', ['attribute' => date_create($date_system)->format('Y/m/d')]));
                }
            }

            if (count((array)$dateUserOrg) > 0) {
                foreach ($dateUserOrg as $key => $value) {
                    // create variables start_date_old
                    $value = (object)$value;
                    if ($upsert_current_date_flag && $key == $index_change) {
                        $value->start_date_old = $request->start_date_old;
                        $value->upsert_flag = true;
                    } else {
                        $value->start_date_old = $value->start_date;
                        $value->upsert_flag = false;
                    }
                    if ($request->start_date_old == $value->start_date) {
                        $value->start_date = $request->start_date;
                    }
                }
                // sort start date in array
                array_multisort(
                    array_column($dateUserOrg, 'start_date'),
                    SORT_ASC,
                    $dateUserOrg
                );

                // edit end date all
                foreach ($dateUserOrg as $i => $value) {
                    $value = (object)$value;
                    if ($i > 0) {
                        $dateUserOrg[$i - 1]->end_date = date_create($value->start_date)->modify('-1 days')->format('Y-m-d');
                    }
                    if ($i == count($dateUserOrg) - 1) {
                        $dateUserOrg[$i]->end_date = '9999-12-31';
                    }
                }

                // update or insert current user with start date old param
                $organization = $request->organization;
                // sort delete flag desc
                array_multisort(
                    array_column($organization, 'delete_flag'),
                    SORT_DESC,
                    $organization
                );

                // check duplicate values in request
                $check_duplicate = [];
                if (count($organization) > 0) {
                    foreach ($organization as $item) {
                        $item = (object)$item;
                        if ($item->delete_flag == 0) {
                            $organization_org = $item->org_cd;
                            if (in_array($organization_org, $check_duplicate)) {
                                return $this->responseErrorValidate(__('usermanagement.duplicate', ['attribute' => $item->org_label, 'name' => '組織']));
                            } else {
                                $check_duplicate[] = $organization_org;
                            }
                        }
                    }
                }
                // insert , update and delete database
                if ($upsert_current_date_flag) {
                    $this->userManagement->deleteUserOrganization($request->user_cd, $request->start_date, null);
                    $this->userManagement->deleteUserOrganization($request->user_cd, $request->start_date_old, null);
                } else {
                    $this->userManagement->deleteUserOrganization($request->user_cd, $request->start_date_old, null);
                }
                if ($date_system == $request->start_date) {
                    $this->userManagement->deleteMasterUserOrgByUserCd($request->user_cd);
                }
                if (count($organization) > 0) {
                    foreach ($organization as $item) {
                        $item = (object)$item;
                        $this->userManagement->insertUserOrganization($request->user_cd, $request->start_date, $request->end_date, $item->org_cd, $item->main_flag, $item->user_post_type);
                        if ($date_system == $request->start_date) {
                            $this->userManagement->updateMasterUserOrg($request->user_cd, $item->org_cd, $item->user_post_type, $item->main_flag);
                        }
                    }
                }

                // update end date all of user
                $edit_mode_flag = false;
                $upsertFinal = [];
                foreach ($dateUserOrg as $i => $value) {
                    $value = (object)$value;
                    if ($value->upsert_flag) {
                        continue;
                    }
                    if (count($dateUserOrg) == 2) { //create version
                        if (!$upsert_current_date_flag) {
                            $end_date = $value->end_date;
                        } else {
                            $end_date = '9999-12-31';
                        }
                    } else { //update version
                        $edit_mode_flag = true;
                        $end_date = $value->end_date;
                    }
                    $this->userManagement->updateOrgEndDate($request->user_cd, $value->start_date, $end_date);
                    if ($edit_mode_flag && $upsert_current_date_flag) {
                        if ($value->start_date > $date_system && $value->end_date <= $request->end_date && $request->flag_change) {
                            $this->userManagement->deleteUserOrganization($request->user_cd, $value->start_date, null);
                        } else {
                            array_push($upsertFinal, $value);
                        }
                    }
                }
                //upsert final
                foreach ($upsertFinal as $i => $value) {
                    $value = (object)$value;
                    if ($i > 0) {
                        $upsertFinal[$i - 1]->end_date = date_create($value->start_date)->modify('-1 days')->format('Y-m-d');
                    }
                    if ($i == count($upsertFinal) - 1) {
                        $upsertFinal[$i]->end_date = '9999-12-31';
                    }
                }
                foreach ($upsertFinal as $i => $value) {
                    $value = (object)$value;
                    $this->userManagement->updateOrgEndDate($request->user_cd, $value->start_date, $value->end_date);
                }
                //end upsert final
            }
            DB::commit();
            return $this->responseCreated(__('usermanagement.update'));
        } catch (Exception $e) {
            //Error
            DB::rollBack();
            throw $e;
            return $this->responseSystemError(__('usermanagement.system_error'));
        }
    }

    /**
     *  @OA\Delete(
     *      path="/api/user-management/delete-user-organization",
     *      operationId="deleteUserOrganization",
     *      tags={"I01-S01"},
     *      summary="User Management",
     *      description="Delete User Organization",
     *      security={{"jwt": {}}},
     *      @OA\RequestBody(
     *          description="Delete User Organization params",
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
     *      @OA\Response(response=204, description="Delete successfully",
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
    public function deleteUserOrganization(DeleteUserOrganizationRequest $request)
    {
        DB::beginTransaction();
        try {
            $dataUser = $this->userManagement->getUserOrgUpdate($request->user_cd, $request->start_date);
            if (count((array)$dataUser) > 0) {
                $this->userManagement->updateOrgEndDate($request->user_cd, $dataUser[0]->start_date, $request->end_date);
            }
            $this->userManagement->deleteUserOrganization($request->user_cd, $request->start_date, '');
            DB::commit();
            return $this->responseNoContent(__('usermanagement.delete'));
        } catch (Exception $e) {
            //Error
            DB::rollBack();
            throw $e;
            return $this->responseSystemError(__('usermanagement.system_error'));
        }
    }



    /**
     *      @OA\GET(
     *      path="/api/user-management/get-approval-user",
     *      operationId="getApprovalUser",
     *      tags={"I01-S01"},
     *      summary="User Management",
     *      description="Get Approval User",
     *      security={{"jwt": {}}},
     *      @OA\Parameter(name="user_cd",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="user_name",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="org_cd",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="approval_work_type",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="limit",in="query", @OA\Schema(type="number")),
     *      @OA\Parameter(name="offset",in="query", @OA\Schema(type="number")),
     *      @OA\Response(response=200, description="Response successfully",
     *      @OA\JsonContent(
     *      type="object",
     *      example={
     *            "status": "200",
     *             "message": "success",
     *             "data": {
     *                  "records": {
     *                      {
     *                          "user_cd": "10225",
     *                          "user_name": "山畑 茂美",
     *                          "org_label": "営業企画部",
     *                          "definition_label": "本社スタッフ",
     *                          "approval_current": {
     *                              {
     *                                  "start_date": "2021/11/18",
     *                                  "end_date": "2022/11/18",
     *                                  "approval_layer_num": {
     *                                      {
     *                                          "approval_layer_num": 1,
     *                                          "approval_user": {
     *                                              {
     *                                                  "approval_user_cd": "10220",
     *                                                  "user_name": "柿島 佳花",
     *                                                  "definition_label": "本社スタッフ",
     *                                                  "org_label": "マーケティング部"
     *                                              }
     *                                          }
     *                                      },
     *                                      {
     *                                          "approval_layer_num": 2,
     *                                          "approval_user": {
     *                                              {
     *                                                  "approval_user_cd": "10218",
     *                                                  "user_name": "炭谷 美翔",
     *                                                  "definition_label": "本社スタッフ",
     *                                                  "org_label": "マーケティング部"
     *                                              }
     *                                          }
     *                                      }
     *                                  }
     *                              }
     *                          },
     *                          "approval_reservation": {
     *                              {
     *                                  "start_date": "2022/11/19",
     *                                  "end_date": "2023/11/18",
     *                                  "approval_layer_num": {
     *                                      {
     *                                          "approval_layer_num": 1,
     *                                          "approval_user": {
     *                                              {
     *                                                  "approval_user_cd": "10224",
     *                                                  "user_name": "竹永 嘉",
     *                                                  "definition_label": "本社スタッフ",
     *                                                  "org_label": "営業企画部"
     *                                              }
     *                                          }
     *                                      },
     *                                      {
     *                                          "approval_layer_num": 2,
     *                                          "approval_user": {
     *                                              {
     *                                                  "approval_user_cd": "10224",
     *                                                  "user_name": "竹永 嘉",
     *                                                  "definition_label": "本社スタッフ",
     *                                                  "org_label": "営業企画部"
     *                                              }
     *                                          }
     *                                      }
     *                                  }
     *                              },
     *                              {
     *                                  "start_date": "2023/11/20",
     *                                  "end_date": "2024/11/20",
     *                                  "approval_layer_num": {
     *                                      {
     *                                          "approval_layer_num": 2,
     *                                          "approval_user": {
     *                                              {
     *                                                  "approval_user_cd": "10224",
     *                                                  "user_name": "竹永 嘉",
     *                                                  "definition_label": "本社スタッフ",
     *                                                  "org_label": "営業企画部"
     *                                              }
     *                                          }
     *                                      }
     *                                  }
     *                              }
     *                          }
     *                      }
     *                  },
     *                  "pagination": {
     *                      "total_items": 1,
     *                      "total_pages": 1,
     *                      "previous_page": 1,
     *                      "next_page": 1,
     *                      "current_page": 1,
     *                      "first_page": 1,
     *                      "last_page": 1
     *                  }
     *                       }
     *                  }
     *              )
     *        ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     *    )
     */
    public function getApprovalUser(Request $request)
    {
        $limit = $request->filled('limit') ? $request->limit : 100;
        $offset = $request->filled('offset') ? $request->offset : 0;
        $request->user_name = $this->convert_kana($request->user_name);
        $data = $this->userManagement->getApprovalUser($request->user_cd, $request->user_name, $request->org_cd, $request->approval_work_type, $request->parameter_value ?? 10, $limit, $offset);
        return $this->responseSuccess("success", $data);
    }

    /**
     *  @OA\POST(
     *      path="/api/user-management/update-approval-user",
     *      operationId="updateApprovalUser",
     *      tags={"I01-S01"},
     *      summary="User Management",
     *      description="Update Approval User",
     *      security={{"jwt": {}}},
     *      @OA\RequestBody(
     *          description="Update Approval User params",
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(
     *                      property="user_cd",
     *                      type="string",
     *                      description="The user code",
     *                      default="10001"
     *                  ),
     *                  @OA\Property(
     *                      property="approval_work_type",
     *                      type="number",
     *                      description="approval work type",
     *                      default="1"
     *                  ),
     *                  @OA\Property(
     *                      property="start_date",
     *                      type="date",
     *                      description="Start Date",
     *                      default="2023-01-01"
     *                  ),
     *                  @OA\Property(
     *                      property="end_date",
     *                      type="date",
     *                      description="end Date",
     *                      default="2023-01-01"
     *                  ),
     *                  @OA\Property(
     *                      property="start_date_old",
     *                      type="date",
     *                      description="Start Date old",
     *                      default="2023-01-01"
     *                  ),
     *                  @OA\Property(
     *                      property="approval_layer_num",
     *                      type="string",
     *                      description="Approval Layer Num",
     *                      default="[]"
     *                  ),
     *                  @OA\Property(
     *                      property="flag_change",
     *                      type="string",
     *                      description="flag change",
     *                      default="0 : insert , 1 : update"
     *                  )
     *              ),
     *          ),
     *      ),
     *      @OA\Response(response=201, description="Saved successfully",
     *             @OA\JsonContent(
     *              type="object",
     *              example={
     *                       "status": "201",
     *                       "message": "正常に保存しました。",
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
    public function updateApprovalUser(UpdateApprovalUserRequest $request)
    {
        DB::beginTransaction();
        try {
            $request->start_date = $this->responseDateTimeFormat($request->start_date, 'Y-m-d');
            $request->start_date_old = $this->responseDateTimeFormat($request->start_date_old, 'Y-m-d');
            $request->end_date = $this->responseDateTimeFormat($request->end_date, 'Y-m-d');

            $date_system = date_create($this->systemParameterService->getCurrentSystemDateTime())->format('Y-m-d');
            $dateApprovalUser = $this->userManagement->getDateApprovalUser($request->user_cd, $request->approval_work_type, $date_system);

            if (!in_array($request->start_date_old, array_column($dateApprovalUser, 'start_date'))) {
                $dateApprovalUser[count($dateApprovalUser)] = (object)['start_date' => date_create($request->start_date)->format('Y-m-d'), 'end_date' => '9999-12-31'];
            } else {
                if ($request->flag_change == 1) {
                    $index_date_current = array_search($request->start_date_old, array_column($dateApprovalUser, 'start_date'));
                    $dateApprovalUser[$index_date_current]->start_date = $request->start_date;
                } else {
                    $dateApprovalUser[count($dateApprovalUser)] = (object)['start_date' => date_create($request->start_date)->format('Y-m-d'), 'end_date' => '9999-12-31'];
                }
            }

            $index_change = array_search($request->start_date, array_column($dateApprovalUser, 'start_date'));
            $upsert_current_date_flag = false;
            if ($index_change !== false) {
                $start_date_change = $dateApprovalUser[$index_change]->start_date;
                $quantity_date_change = array_count_values(array_column($dateApprovalUser, 'start_date'))[$start_date_change];
                if ($quantity_date_change > 1) {
                    if ($date_system != $start_date_change) {
                        return $this->responseErrorValidate(__('usermanagement.by_date', ['attribute' => date_create($start_date_change)->format('Y/m/d')]));
                    } else {
                        $upsert_current_date_flag = true;
                    }
                }
                if ($request->start_date < $date_system) {
                    return $this->responseErrorValidate(__('usermanagement.system_date', ['attribute' => date_create($date_system)->format('Y/m/d')]));
                }
            }

            if (count((array)$dateApprovalUser) > 0) {
                foreach ($dateApprovalUser as $key => $value) {
                    $value = (object)$value;
                    if ($upsert_current_date_flag && $key == $index_change) {
                        $value->start_date_old = $request->start_date_old;
                        $value->upsert_flag = true;
                    } else {
                        $value->start_date_old = $value->start_date;
                        $value->upsert_flag = false;
                    }
                    if ($request->start_date_old == $value->start_date) {
                        $value->start_date = $request->start_date;
                    }
                }
                // sort start date in array
                array_multisort(
                    array_column($dateApprovalUser, 'start_date'),
                    SORT_ASC,
                    $dateApprovalUser
                );

                // edit end date all
                foreach ($dateApprovalUser as $i => $value) {
                    $value = (object)$value;
                    if ($i > 0) {
                        $dateApprovalUser[$i - 1]->end_date = date_create($value->start_date)->modify('-1 days')->format('Y-m-d');
                    }
                    if ($i = count($dateApprovalUser) - 1) {
                        $dateApprovalUser[$i]->end_date = '9999-12-31';
                    }
                }
                // update or insert current user with start date old param
                $approval_layer_num = $request->approval_layer_num;

                if (count($approval_layer_num) > 0) {
                    if ($upsert_current_date_flag) {
                        $this->userManagement->deleteApprovalUser($request->user_cd, $request->approval_work_type, $request->start_date, null, null);
                        $this->userManagement->deleteApprovalUser($request->user_cd, $request->approval_work_type, $request->start_date_old, null, null);
                    } else {
                        $this->userManagement->deleteApprovalUser($request->user_cd, $request->approval_work_type, $request->start_date_old, null, null);
                    }
                    foreach ($approval_layer_num as $item) {
                        $item = (object)$item;
                        if (count($item->approval_user) > 0) {
                            // sort delete flag desc
                            array_multisort(
                                array_column($item->approval_user, 'delete_flag'),
                                SORT_DESC,
                                $item->approval_user
                            );
                            $check_duplicate = [];
                            foreach ($item->approval_user as $approval_user) {
                                $approval_user = (object)$approval_user;
                                $organization_org = $approval_user->approval_user_cd . '/' . $approval_user->org_cd;
                                if (in_array($organization_org, $check_duplicate)) {
                                    return $this->responseErrorValidate(__('usermanagement.duplicate', ['attribute' => 'ユーザコード（承認者）', 'name' => '(' . $approval_user->user_name . ')']));
                                } else {
                                    $check_duplicate[] = $organization_org;
                                }
                                $this->userManagement->insertApprovalUser($request->user_cd, $request->approval_work_type, $request->start_date, $request->end_date, $item->approval_layer_num, $approval_user->approval_user_cd);
                            }
                        }
                    }
                }
                // update end date all of user
                $edit_mode_flag = false;
                $upsertFinal = [];
                foreach ($dateApprovalUser as $i => $value) {
                    $value = (object)$value;
                    if ($value->upsert_flag) {
                        continue;
                    }
                    if (count($dateApprovalUser) == 2) { //create version
                        if (!$upsert_current_date_flag) {
                            $end_date = $value->end_date;
                        } else {
                            $end_date = '9999-12-31';
                        }
                    } else { //update version
                        $edit_mode_flag = true;
                        $end_date = $value->end_date;
                    }
                    $this->userManagement->updateApprovalUserEndDate($request->user_cd, $request->approval_work_type, $value->start_date, $end_date);
                    if ($edit_mode_flag && $upsert_current_date_flag) {
                        if ($value->start_date > $date_system && $value->end_date <= $request->end_date && $request->flag_change) {
                            $this->userManagement->deleteApprovalUser($request->user_cd, $request->approval_work_type, $value->start_date, null, null);
                        } else {
                            array_push($upsertFinal, $value);
                        }
                    }
                }
                //upsert final
                foreach ($upsertFinal as $i => $value) {
                    $value = (object)$value;
                    if ($i > 0) {
                        $upsertFinal[$i - 1]->end_date = date_create($value->start_date)->modify('-1 days')->format('Y-m-d');
                    }
                    if ($i == count($upsertFinal) - 1) {
                        $upsertFinal[$i]->end_date = '9999-12-31';
                    }
                }
                foreach ($upsertFinal as $i => $value) {
                    $value = (object)$value;
                    $this->userManagement->updateApprovalUserEndDate($request->user_cd, $request->approval_work_type, $value->start_date, $value->end_date);
                }
            }
            DB::commit();
            return $this->responseCreated(__('usermanagement.update'));
        } catch (Exception $e) {
            //Error
            DB::rollBack();
            throw $e;
            return $this->responseSystemError(__('usermanagement.system_error'));
        }
    }

    /**
     *  @OA\Delete(
     *      path="/api/user-management/delete-approval-user",
     *      operationId="deleteApprovalUser",
     *      tags={"I01-S01"},
     *      summary="User Management",
     *      description="Delete User Approval",
     *      security={{"jwt": {}}},
     *      @OA\RequestBody(
     *          description="Delete User Approval Params",
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
     *                      property="approval_work_type",
     *                      type="string",
     *                      description="Approval work type",
     *                      default="1"
     *                  ),
     *                  @OA\Property(
     *                      property="start_date",
     *                      type="date",
     *                      description="The start date",
     *                      default="2021-11-10"
     *                  ),
     *                  @OA\Property(
     *                      property="end_date",
     *                      type="date",
     *                      description="The end date",
     *                      default="2021-11-10"
     *                  )
     *              ),
     *          ),
     *      ),
     *      @OA\Response(response=204, description="Delete successfully",
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
    public function deleteApprovalUser(DeleteApprovalUserRequest $request)
    {
        DB::beginTransaction();
        try {
            $dataUser = $this->userManagement->getApprovalUserUpdate($request->user_cd, $request->approval_work_type, $request->start_date);
            if (count((array)$dataUser) > 0) {
                $this->userManagement->updateApprovalUserEndDate($request->user_cd, $request->approval_work_type, $dataUser[0]->start_date, $request->end_date);
            }
            $this->userManagement->deleteApprovalUser($request->user_cd, $request->approval_work_type, $request->start_date, '', '');
            DB::commit();
            return $this->responseNoContent(__('usermanagement.delete'));
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
            return $this->responseSystemError(__('usermanagement.system_error'));
        }
    }
}
