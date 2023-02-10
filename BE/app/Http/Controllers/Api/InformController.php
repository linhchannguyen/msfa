<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Services\InformService;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Inform\SearchRequest;
use App\Transformers\InformTransformer;

class InformController extends Controller
{
    private $service;

    public function __construct(InformService $service)
    {
        $this->service = $service;
        $this->middleware('role.not:' . config('role.DEV.CODE'));
    }

    /**
     * @OA\POST(
     *      path="/api/inform",
     *      operationId="searchInform",
     *      tags={"Z02-S03"},
     *      summary="Search inform",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\RequestBody(
     *          description="Read message",
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(
     *                      property="inform_category_cd",
     *                      type="string",
     *                      description="important flag",
     *                      default="[10]"
     *                  ),
     *                  @OA\Property(
     *                      property="inform_datetime_from",
     *                      type="string",
     *                      description="inform datetime from",
     *                      default="2021-01-01"
     *                  ),
     *                  @OA\Property(
     *                      property="inform_datetime_to",
     *                      type="string",
     *                      description="inform datetime to",
     *                      default=""
     *                  ),
     *                  @OA\Property(
     *                      property="archive_flag",
     *                      type="integer",
     *                      description="if archive_flag=1 is archive else unarchive",
     *                      default="0"
     *                  ),
     *                  @OA\Property(
     *                      property="inform_contents",
     *                      type="string",
     *                      description="inform contents",
     *                      default=""
     *                  ),
     *                  @OA\Property(
     *                      property="limit",
     *                      type="string",
     *                      description="limit",
     *                      default="100"
     *                  ),
     *                  @OA\Property(
     *                      property="offset",
     *                      type="string",
     *                      description="offset",
     *                      default="0"
     *                  )
     *              ),
     *          ),
     *      ),
     *      @OA\Response(response=200, description="Successful response",
     *             @OA\JsonContent(
     *              type="object",
     *              example={
     *                     "status":"success",
     *                     "data":{
     *                          {
     *                             "inform_id": 2,
     *                             "inform_category_cd": "10",
     *                             "inform_category_name": "日報提出",
     *                             "inform_datetime": "2021-11-09 14:05:58",
     *                             "inform_user_cd": "10001",
     *                             "inform_contents": "Contenst",
     *                             "archive_flag": 0,
     *                             "informed_flag": 1,
     *                              "read_flag": 0,
     *                              "url": ""
     *                          }
     *                     }
     *                 }
     *              )
     *      ),
     *      @OA\Response(response=400, description="Validation Error")
     * )
     */
    public function searchInform(SearchRequest $request)
    {
        $data = [];
        $request->user_cd = $this->getCurrentUser();
        $limit = $request->filled('limit') ? $request->limit : 100;
        $offset = $request->filled('offset') ? $request->offset : 0;
        $inform_category_cd = $request->inform_category_cd;
        $request->inform_category_cd = !is_array($inform_category_cd) ? [] : $inform_category_cd;
        if (!$request->inform_category_cd) {
            $result['records'] = [];
            $result['pagination'] = [];
        }else{
            $data = $this->service->search($request, $limit, $offset);
            $result['records'] = empty($data['records']) ? [] : InformTransformer::collection($data['records']);
            $result['pagination'] = $data['pagination'];
        }

        return $this->responseSuccess('success', $result);
    }

    /**
     * @OA\PUT(
     *      path="/api/inform/archived",
     *      operationId="archived",
     *      tags={"Z02-S03"},
     *      summary="Archived",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\RequestBody(
     *          description="Read message",
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(
     *                      property="inform_id",
     *                      type="string",
     *                      description="inform id",
     *                      default="[1,2,3]"
     *                  ),
     *                  @OA\Property(
     *                      property="archive_flag",
     *                      type="string",
     *                      description="If archive_flag is archive else unarchive",
     *                      default="1"
     *                  ),
     *                  @OA\Property(
     *                      property="home_screen_flag",
     *                      type="string",
     *                      description="if home_screen_flag=1 then update all inform to archive else unarchive",
     *                      default="1"
     *                  )
     *              ),
     *          ),
     *      ),
     *      @OA\Response(response=201, description="Successful response")
     * )
     */
    public function archived(Request $request)
    {
        $result = false;
        $request->user_cd = $this->getCurrentUser();
        DB::beginTransaction();
        if ((bool)$request->home_screen_flag) {
            $result = $this->service->archiveAll($request->user_cd);
        } else {
            $inform_id = $request->inform_id;
            if (is_array($inform_id)) {
                $count = count($inform_id);
                if ($count > 0) {
                    if ($request->archive_flag === 1 || $request->archive_flag === "1") {
                        $result = $this->service->archived($request);
                    } else if ($request->archive_flag === 0 || $request->archive_flag === "0") {
                        $result = $this->service->unarchived($request);
                    }
                }
            }
        }
        if (!$result) {
            DB::rollBack();
            return $this->responseSystemError(__('inform.system_error'));
        }

        DB::commit();
        return $this->responseCreated(__('inform.update_setting_successfully'));
    }

    /**
     * @OA\PUT(
     *      path="/api/inform/read-inform",
     *      operationId="readInform",
     *      tags={"Z02-S03"},
     *      summary="Read inform",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\RequestBody(
     *          description="Read message",
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(
     *                      property="inform_id",
     *                      type="integer",
     *                      description="inform id",
     *                      default="1"
     *                  )
     *              ),
     *          ),
     *      ),
     *      @OA\Response(response=201, description="Successful response")
     * )
     */
    public function readInform($id)
    {
        $this->service->readInform($id);
        return $this->responseCreated(__('inform.update_successfully'));
    }

    /**
     * @OA\GET(
     *      path="/api/inform/installed",
     *      operationId="installed",
     *      tags={"Z02-S03"},
     *      summary="Get installation information",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\Response(response=200, description="Successful response",
     *          @OA\JsonContent(
     *              type="object",
     *              example={
     *                  "status":"success",
     *                  "data":{
     *                      {
     *                          "inform_category_cd": "10",
     *                          "inform_category_name": "日報提出",
     *                          "checked": 1
     *                      }
     *                  }
     *              }
     *          )
     *      )
     * )
     */
    public function installed()
    {
        $data = $this->service->installed($this->getCurrentUser());

        return $this->responseSuccess('success', $data);
    }

    /**
     * @OA\POST(
     *      path="/api/inform/register",
     *      operationId="registerInformSetting",
     *      tags={"Z02-S03"},
     *      summary="Inform settings",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\RequestBody(
     *          description="Read message",
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(
     *                      property="inform_category_cd",
     *                      type="string",
     *                      description="User refuses to receive notifications of this type of notification",
     *                      default="[10,100,110,120,130,140,150,160,170]"
     *                  )
     *              ),
     *          ),
     *      ),
     *      @OA\Response(response=201, description="Successful response"),
     *      @OA\Response(response=500, description="System Error or Unknow Error Response")
     * )
     */
    public function registerInformSetting(Request $request)
    {
        $request->user_cd = $this->getCurrentUser();
        $inform_category_cd = $request->inform_category_cd;
        $result = false;
        DB::beginTransaction();
        if (is_array($inform_category_cd)) {
            $count = count($inform_category_cd);
            $result = $this->service->delete($request->user_cd);
            if ($count > 0) {
                $data = [];
                foreach ($inform_category_cd as $value) {
                    array_push($data, [
                        'inform_category_cd' => $value,
                        'user_cd' => $request->user_cd
                    ]);
                }
                $result = $this->service->register($data);
            }
        }
        if (!$result) {
            DB::rollBack();
            return $this->responseSystemError(__('inform.system_error'));
        }

        DB::commit();
        return $this->responseCreated(__('inform.update_setting_successfully'));
    }
}
