<?php

namespace App\Http\Controllers\Api;

use stdClass;
use Illuminate\Http\Request;
use App\Services\PersonSearchService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\PersonSearch\PersonSearchRequest;
use App\Http\Requests\Api\PersonSearch\PersonInformationRequest;
use App\Http\Requests\Api\PersonSearch\SaveMedicalOfficePersonRequest;
use App\Http\Requests\Api\PersonSearch\DepartmentPersonDetailRequest;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PersonSearchController extends Controller
{
    private $service;

    public function __construct(PersonSearchService $service)
    {
        $this->service = $service;
        $this->middleware('role.not:' . config('role.DEV.CODE'));
    }

    /**
     * @OA\GET(
     *      path="/api/person-search",
     *      operationId="getAllPerson",
     *      tags={"H02-S01"},
     *      summary="Get All Person",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\Parameter(name="person_cd",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="person_name",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="facility_short_name",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="facility_cd",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="user_cd",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="option",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="move_flag",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="ultmarc_delete_flag",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="limit",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="offset",in="query", @OA\Schema(type="string")),
     *      @OA\Response(response=200, description="Successful response",
     *          @OA\JsonContent(
     *              type="object",
     *              example={
     *                  "status":"success",
     *                  "data":{
     *                  }
     *              }
     *          )
     *      ),
     *      @OA\Response(response=403, description="Forbidden Response")
     * )
     */
    public function getData(Request $request)
    {
        $request->limit = $request->filled('limit') ? $request->limit : 100;
        $request->offset = $request->filled('offset') ? $request->offset : 0;
        $request->user_login = $this->getCurrentUser();
        $data = $this->service->getDataPerson($request);
        return $this->responseSuccess('success', $data);
    }

    /**
     * @OA\GET(
     *      path="/api/person-search/information",
     *      operationId="getPersonInformation",
     *      tags={"H02-S02"},
     *      summary="Get Person Information",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\Parameter(name="person_cd",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="ultmarc_delete_flag",in="query", @OA\Schema(type="string")),
     *      @OA\Response(response=200, description="Successful response",
     *          @OA\JsonContent(
     *              type="object",
     *              example={
     *                  "status":"success",
     *                  "data":{
     *                  }
     *              }
     *          )
     *      ),
     *      @OA\Response(response=403, description="Forbidden Response")
     * )
     */
    public function getPersonInformation(PersonInformationRequest $request)
    {
        $data = $this->service->getPersonInformation($request);
        return $this->responseSuccess('success', $data);
    }

    /**
     * @OA\GET(
     *      path="/api/person-search/basic-information",
     *      operationId="getPersonDetail",
     *      tags={"H02-S02"},
     *      summary="Get Person Detail",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\Parameter(name="person_cd",in="query", @OA\Schema(type="string")),
     *      @OA\Response(response=200, description="Successful response",
     *          @OA\JsonContent(
     *              type="object",
     *              example={
     *                  "status":"success",
     *                  "data":{
     *                  }
     *              }
     *          )
     *      ),
     *      @OA\Response(response=403, description="Forbidden Response")
     * )
     */
    public function getPersonDetail(PersonSearchRequest $request)
    {
        $data = $this->service->getPersonDetail($request);
        return $this->responseSuccess('success', $data);
    }

    /**
     * @OA\GET(
     *      path="/api/person-search/basic-information/department",
     *      operationId="getDepartmentPersonDetail",
     *      tags={"H02-S02"},
     *      summary="get Department Person Detail",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\Parameter(name="facility_cd",in="query", @OA\Schema(type="string")),
     *      @OA\Response(response=200, description="Successful response",
     *          @OA\JsonContent(
     *              type="object",
     *              example={
     *                  "status":"success",
     *                  "data":{
     *                  }
     *              }
     *          )
     *      ),
     *      @OA\Response(response=403, description="Forbidden Response")
     * )
     */
    public function getDepartmentPersonDetail(DepartmentPersonDetailRequest $request)
    {
        $data = $this->service->getDepartmentPersonDetail($request);
        return $this->responseSuccess('success', $data);
    }

    /**
     * @OA\PUT(
     *      path="/api/person-search/basic-information/medical-office",
     *      operationId="saveMedicalOffice",
     *      tags={"H02-S02"},
     *      summary="Save Medical Office",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\Parameter(name="person_cd",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="medical_office_facility_cd",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="medical_office_department_cd",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="medical_office_name",in="query", @OA\Schema(type="string")),
     *      @OA\Response(response=200, description="Successful response",
     *          @OA\JsonContent(
     *              type="object",
     *              example={
     *                  "status":"success",
     *                  "data":{
     *                  }
     *              }
     *          )
     *      ),
     *      @OA\Response(response=403, description="Forbidden Response")
     * )
     */
    public function saveMedicalOffice(SaveMedicalOfficePersonRequest $request)
    {
        DB::beginTransaction();
        try {
            $this->service->saveMedicalOffice($request);
            DB::commit();
            return $this->responseCreated('success');
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
            $this->responseSystemError(__('validation.MSFA0029', ['attribute' => "product"]));
        }
    }

    /**
     * @OA\GET(
     *      path="/api/person-search/working-individual",
     *      operationId="WorkingIndividualPerson",
     *      tags={"H02-S03"},
     *      summary="Get Working Individual Person",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\Parameter(name="person_cd",in="query", @OA\Schema(type="string")),
     *      @OA\Response(response=200, description="Successful response",
     *          @OA\JsonContent(
     *              type="object",
     *              example={
     *                  "status":"success",
     *                  "data":{
     *                  }
     *              }
     *          )
     *      ),
     *      @OA\Response(response=403, description="Forbidden Response")
     * )
     */
    public function getWorkingIndividual(PersonSearchRequest $request)
    {
        $data = $this->service->getWorkingIndividual($request);
        return $this->responseSuccess('success', $data);
    }
}
