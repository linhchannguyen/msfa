<?php

namespace App\Http\Controllers\Api;

use App\Traits\Base64StringFileTrait;
use App\Services\AuthService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\ChangePasswordRequest;
use App\Http\Requests\Api\Auth\ProxyUserSelectionRequest;
use App\Http\Requests\Api\Auth\UserLoginRequest;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Exception;
use App\Traits\RolePolicyTrait;
use Adldap\Laravel\Facades\Adldap;
use App\Services\RolePolicyService;
class AuthController extends Controller
{
    use RolePolicyTrait;
    use Base64StringFileTrait;

    private $auth, $role , $role_user;

    public function __construct(AuthService $auth , RolePolicyService $role_user)
    {
        $this->auth = $auth;
        $this->role_user = $role_user;
        $this->middleware('role.not:' . config('role.DEV.CODE'))->only(['login', 'changePassword']);
        $this->middleware('role.has:' . config('role.SYS_MG.CODE'))->only(['proxyUserSelection']);
    }

    /**
     * @OA\Post(
     *      path="/api/login",
     *      tags={"Z01-S01"},
     *      summary="Login User",
     *      description="User Login System",
     *      @OA\RequestBody(
     *          description="Login params",
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(
     *                      property="user_cd",
     *                      type="string",
     *                      description="The user cd",
     *                      default="tutx"
     *                  ),
     *                  @OA\Property(
     *                      property="password_hash",
     *                      type="string",
     *                      description="The password",
     * *                    default="12345678"
     *                  ),
     *              ),
     *          ),
     *      ),
     *      @OA\Response(response=200, description="Response successfully",
     *             @OA\JsonContent(
     *              type="object",
     *              example={
     *                     "status": "201",
     *                      "message": "正常にサインインしました。",
     *                      "data": {
     *                           "user_cd": "10002",
     *                           "user_name": "安永 みづほ",
     *                           "mail_address": "10002@gmail.com",
     *                           "require_change_flag": false,
     *                           "avatar_image_data": "https://bu5-msfa.s3.ap-northeast-1.amazonaws.com/profile/Capture.PNG",
     *                           "avatar_image_type": "image/png",
     *                           "org_cd": "10000",
     *                           "org_short_name": "営業部",
     *                           "org_label": "営業部門",
     *                           "layer_num": 1,
     *                           "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvYXBpXC9sb2dpbiIsImlhdCI6MTY1MTgyNTc1MywiZXhwIjoxNjU0NDE3NzUzLCJuYmYiOjE2NTE4MjU3NTMsImp0aSI6IkE3aVZTdGpqb0FodHNoVWkiLCJzdWIiOiIxMDAwMiIsInBydiI6ImYzNjk0YTMwMWE4NDJhNTgxMWQ5M2Q3MWVlZmJjM2VhMDY1Y2IwYzUifQ.ZttNyefaVnNAzpDPSreV36VdOkA2SpMxyHjVj2MVc6M"
     *                       }
     *                 }
     *              )
     *        ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     * )
     */

    public function login(UserLoginRequest $request)
    {
        try {
            $credentials = $request->only('user_cd', 'password_hash');
            $credentials['is_user'] = true;
            $credentials['is_develop'] = false;
            $user_cd = $request->user_cd;
            $user_login = 'is_user';
            $customClaims = ['sub' => $request->user_cd];
            $check_user_exist = $this->auth->login($user_cd);

            // get infor user login
            $data_user_temp =  $this->auth->getInfoUserLogin($user_cd);

            // validate record == 0
            if (count((array)$data_user_temp) == 0) {
                return $this->responseErrorValidate(__('auth.get_info_none'));
                // validate record >= 2
            } elseif (count((array)$data_user_temp) >= 2) {
                return $this->responseSystemError(__('auth.get_infos'));
            }else if (empty($data_user_temp[0]->org_cd ?? '')){
                return $this->responseErrorValidate(__('auth.user_org_not_exists'));
            }

            if (empty($check_user_exist->user_cd ?? '') || (!password_verify($request->password_hash ?? null, $check_user_exist->password ?? ''))) {
                $data['user_cd'] = [__('auth.invalid_token')];
                $data['password_hash'] = [__('auth.invalid_token')];
                return $this->responseUnauthorized(__('auth.invalid_token'), $data);
            } elseif (!$token = JWTAuth::claims($customClaims)->attempt($credentials)) {
                return $this->responseUnauthorized(__('auth.invalid_token'));
            }

            // huynh - login third-party - start
            if ( config('ldap.use_ldap') ) {
                $loginThirdParty = Adldap::auth()->attempt($user_cd, $request->password_hash);
                if ( !$loginThirdParty ) {
                    return $this->responseUnauthorized(__('auth.invalid_token'));
                }
            }
            // huynh - login third-party - end

            $data_user =  $this->getInfoCurrentUser($user_cd, $user_login);

            //get screen and menu
            $role_user = $this->role_user->getRoleList($user_cd);
            if(count($role_user) == 0){
                return $this->responseErrorValidate(__('auth.role_login'));
            }
            $data = $data_user ? [
                'user_cd' => $data_user->user_cd,
                'user_name' => $data_user->user_name,
                'mail_address' => $data_user->mail_address,
                'require_change_flag' => $data_user->require_change_flag == 1 ?? false,
                'avatar_image_data' => $this->encodeBase64String($data_user->avatar_image_data, $token),
                'avatar_image_type' => $data_user->avatar_image_type,
                'org_cd' => $data_user->org_cd,
                'org_short_name' => $data_user->org_short_name,
                'org_label' => $data_user->org_label,
                'layer_num' => $data_user->layer_num
            ] : [];

            $data['access_token'] = $token;
            return $this->responseSuccess(__('auth.login_success'), $data);
        } catch (JWTException $e) {
            throw $e;
            return $this->responseSystemError(__('auth.system_error'));
        }
    }

    /**
     * @OA\Post(
     *      path="api/change-password",
     *      tags={"Z01-S01"},
     *      summary="Change Password",
     *      description="Change Password System",
     *      security={{"jwt": {}}},
     *      @OA\RequestBody(
     *          description="Change Password params",
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(
     *                      property="current_password",
     *                      type="string",
     *                      description="Current Password",
     *                      default="12345678"
     *                  ),
     *                  @OA\Property(
     *                      property="new_password",
     *                      type="string",
     *                      description="New Password",
     * *                    default="1234567a"
     *                  ),
     *              ),
     *          ),
     *      ),
     *      @OA\Response(response=201, description="Response successfully",
     *             @OA\JsonContent(
     *              type="object",
     *              example={
     *                      "status": "201",
     *                      "message": "正常にサインインしました。",
     *                      "data": {}
     *                 }
     *              )
     *        ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     *    )
     */
    public function changePassword(ChangePasswordRequest $request)
    {
        try {
            $user_login = 'is_user';
            $user_cd =  $this->getCurrentUser();
            $user =  $this->getInfoCurrentUser($user_cd, $user_login);

            if (!$password_verify = password_verify($request->current_password ?? null, $user->password ?? '')) {
                $data['current_password'] = [__('auth.password_error')];
                return $this->responseErrorValidate(__('auth.password_error'), $data);
            } elseif ($request->current_password == $request->new_password) {
                $data['new_password'] = [__('auth.password_confirm')];
                return $this->responseErrorValidate(__('auth.password_confirm'), $data);
            }

            $new_password = bcrypt($request->new_password);
            if ($password_verify) {
                $this->auth->ChangePassword($user_cd, $new_password);
            }
            return $this->responseCreated(__('auth.change_password_success'));
        } catch (Exception $e) {
            //Error
            throw $e;
            return $this->responseSystemError(__('auth.system_error'));
        }
    }

    /**
     * @OA\Post(
     *      path="/api/proxy-user-selection",
     *      tags={"Z01-S03"},
     *      summary="Proxy User Selection",
     *      description="Proxy User Selection",
     *      security={{"jwt": {}}},
     *      @OA\RequestBody(
     *          description="Proxy User params",
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(
     *                      property="user_cd",
     *                      type="string",
     *                      description="User Cd",
     *                      default="tutx"
     *                  ),
     *              ),
     *          ),
     *      ),
     *      @OA\Response(response=201, description="Response successfully",
     *             @OA\JsonContent(
     *              type="object",
     *              example={
     *                      "status": "200",
     *                      "message": "正常にサインインしました。",
     *                      "data": {
     *                                  "user_cd": "10216",
     *                                  "user_name": "鷲野 恵理花",
     *                                  "mail_address": "10216@dummy.ne.jp",
     *                                  "require_change_flag": true,
     *                                  "role": null,
     *                                  "role_name": null,
     *                                  "avatar_image_data": "",
     *                                  "avatar_image_type": null,
     *                                  "org_cd": "10000",
     *                                  "org_short_name": "営業部",
     *                                  "org_label": "営業部門",
     *                                  "layer_num": "1",
     *                                  "access_token_proxy": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvYXBpXC9wcm94eS11c2VyLXNlbGVjdGlvbiIsImlhdCI6MTYzNjUxMjUyMSwiZXhwIjoxNjM5MTA0NTIxLCJuYmYiOjE2MzY1MTI1MjEsImp0aSI6IjBkaW1lZnNndkJ4bUVXUFAiLCJzdWIiOiIxMDIxNiIsInBydiI6ImYzNjk0YTMwMWE4NDJhNTgxMWQ5M2Q3MWVlZmJjM2VhMDY1Y2IwYzUifQ.VJMfwy0xWbjvnlA_S45lJoQHg9Dw7icX5rG6aKQEKcQ"
     *                     }
     *                 }
     *              )
     *        ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     *    )
     */
    public function proxyUserSelection(ProxyUserSelectionRequest $request)
    {
        try {
            $user_cd = $request->user_cd;
            $credentials['user_cd'] = $user_cd;
            $credentials['is_user'] = true;
            $credentials['is_develop'] = false;
            $credentials['is_proxy'] = true;
            $customClaims = ['sub' => $user_cd];

            if ($this->isProxyUser()) {
                return $this->responseErrorValidate(__('auth.check_not_r01'));
            }

            $user_cd_login =  $this->getCurrentUser();

            if ($request->user_cd == $user_cd_login) {
                return $this->responseErrorValidate(__('auth.check_role_dup'));
            }

            $data_user = $this->auth->getInfoUser($user_cd);
            if (empty($data_user->user_cd ?? '')) {
                $data['user_cd'] = [__('auth.check_user_cd', ['attribute' => 'ユーザコード'])];
                return $this->responseErrorValidate(__('auth.check_user_cd', ['attribute' => 'ユーザコード']), $data);
            } else if (empty($data_user->org_cd ?? '')){
                return $this->responseErrorValidate(__('auth.user_org_not_exists'));
            }

            if (!$token = JWTAuth::claims($customClaims)->attempt($credentials)) {
                return $this->responseErrorValidate(__('auth.invalid_token'));
            }

            $role_user = $this->role_user->getRoleList($user_cd);
            if(count($role_user) == 0){
                return $this->responseErrorValidate(__('auth.role_login'));
            }

            $data = $data_user ? [
                'user_cd' => $data_user->user_cd,
                'user_name' => $data_user->user_name,
                'mail_address' => $data_user->mail_address,
                'require_change_flag' => $data_user->require_change_flag == 1 ?? false,
                'avatar_image_data' =>  $this->encodeBase64String($data_user->avatar_image_data),
                'avatar_image_type' => $data_user->avatar_image_type,
                'org_cd' => $data_user->org_cd,
                'org_short_name' => $data_user->org_short_name,
                'org_label' => $data_user->org_label,
                'layer_num' => $data_user->layer_num
            ] : null;

            $data['access_token_proxy'] = $token;
            return $this->responseSuccess(__('auth.login_success'), $data);
        } catch (JWTException $e) {
            return $this->responseSystemError(__('auth.system_error'));
        }
    }
}
