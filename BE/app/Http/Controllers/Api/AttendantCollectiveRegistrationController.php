<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\AttendantCollectiveRegistration\ImportCsvRequest;
use App\Imports\AttendantCollectiveRegistrationImport;
use App\Services\AttendantCollectiveRegistrationService;
use App\Traits\DateTimeTrait;
use Exception;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;

class AttendantCollectiveRegistrationController extends Controller
{
    use DateTimeTrait;
    private $service;

    public function __construct(AttendantCollectiveRegistrationService $service)
    {
        $this->service = $service;
        $this->middleware('role.has:' . config('role.CONVENTION_ATTENDEE_MG.CODE') . ',' . config('role.SYS_MG.CODE'));
    }

    /**
     *  @OA\POST(
     *      path="/api/attendant-collective-registration/import-csv",
     *      operationId="importCsv",
     *      tags={"C01-S05"},
     *      summary="Attendant Collective Registration",
     *      description="Attendant Collective Registration",
     *      security={{"jwt": {}}},
     *      @OA\RequestBody(
     *          description="Import Csv params",
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(
     *                      property="convention_id",
     *                      type="string",
     *                      description="convention id",
     *                      default="1"
     *                  ),
     *                  @OA\Property(
     *                      property="csv_file",
     *                      type="file",
     *                      description="csv file",
     *                      default=""
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
    public function importCsv(ImportCsvRequest $request)
    {
        DB::beginTransaction();
        try {
            $total = 0;
            $csv_file = $request->csv_file;
            $convention_id = $request->convention_id;
            $data = Excel::toArray(new AttendantCollectiveRegistrationImport, $csv_file);
            $current = $this->currentJapanDateTime('ymdhis');
            $data_import = [];
            $dataExport = [];
            $total = count($data[0]);

            if (count($data[0]) > 0) {
                $facility_list = array_column($data[0], 0);
                $person_list = array_column($data[0], 1);

                // data facility
                $data_facility = count($facility_list) > 0 ? $this->service->getFacilityCd($facility_list) : [];
                $m_facility = array_column($data_facility, 'facility_cd');

                // data person
                $data_person = count($person_list) > 0 ? $this->service->getPersonCd($person_list) : [];
                $m_person = array_column($data_person, 'person_cd');

                // data facility person
                $data_facility_person = (count($facility_list) > 0 && count($person_list)) ? $this->service->getFacilityPerson($facility_list, $person_list) : [];
                $m_facility_person = array_column($data_facility_person, 'facility_person');

                $t_convention_attendee = (count($facility_list) > 0 && count($person_list)) ?  array_column($this->service->getRegistration($facility_list, $person_list, $convention_id), 'facility_person') : [];
                $t_convention_attendee_temp = (count($facility_list) > 0 && count($person_list)) ? array_column($this->service->getRegistrationTemp($facility_list, $person_list, $convention_id), 'facility_person') : [];
                $data_temp = $data[0];
                $compare = [];
                foreach ($data_temp as $value) {
                    $errorMessage = [];
                    $error = 0;
                    $facility_cd = $value[0] ?? 0;
                    $person_cd = $value[1] ?? 0;

                    $exist_1 = in_array($facility_cd, $m_facility);
                    //施設コードがマスタに存在しません
                    if (!$exist_1) {
                        $error++;
                        $errorMessage[] = __('attendantcollectiveregistration.master_exist', ['attribute' => '施設コードがマスタ']);
                        $dataExport[] = [
                            'facility_cd' => $facility_cd,
                            'person_cd' => $person_cd,
                            'error' => __('attendantcollectiveregistration.master_exist', ['attribute' => '施設コードがマスタ'])
                        ];
                        continue;
                    }
                    $key_1 = array_search($facility_cd, $m_facility);

                    $exist_2 = in_array($person_cd, $m_person);
                    //個人コードがマスタに存在しません
                    if (!$exist_2) {
                        $error++;
                        $errorMessage[] = __('attendantcollectiveregistration.master_exist', ['attribute' => '個人コードがマスタ']);
                        $dataExport[] = [
                            'facility_cd' => $facility_cd,
                            'person_cd' => $person_cd,
                            'error' => __('attendantcollectiveregistration.master_exist', ['attribute' => '個人コードがマスタ'])
                        ];
                        continue;
                    }
                    $key_2 = array_search($person_cd, $m_person);

                    $facility_person_temp = $facility_cd . ',' . $person_cd;
                    $exist_3 = in_array($facility_person_temp, $m_facility_person);
                    //該当個人は指定された施設に存在しません
                    if (!$exist_3) {
                        $error++;
                        $errorMessage[] =  __('attendantcollectiveregistration.master_exist', ['attribute' => '該当個人は指定された施設']);
                        $dataExport[] = [
                            'facility_cd' => $facility_cd,
                            'person_cd' => $person_cd,
                            'error' => __('attendantcollectiveregistration.master_exist', ['attribute' => '該当個人は指定された施設'])
                        ];
                        continue;
                    }
                    $key_3 = array_search($facility_person_temp, $m_facility_person);

                    //既に登録済みです
                    if (in_array($facility_person_temp, $t_convention_attendee)) {
                        $error++;
                        $errorMessage[] = __('attendantcollectiveregistration.already_used');
                        $dataExport[] = [
                            'facility_cd' => $facility_cd,
                            'person_cd' => $person_cd,
                            'error' => __('attendantcollectiveregistration.already_used')
                        ];
                        continue;
                    }

                    // 該当個人は登録ファイル内で重複しています
                    // kiểm tra facility_cd ko tồn tại thì mới add vô mảng mới
                    if (count($compare) == 0 || !in_array($person_cd, $compare)) {
                        $compare[] = $person_cd;
                    } else {
                        $error++;
                        $errorMessage[] = __('attendantcollectiveregistration.duplicate');
                        $dataExport[] = [
                            'facility_cd' => $facility_cd,
                            'person_cd' => $person_cd,
                            'error' => __('attendantcollectiveregistration.duplicate')
                        ];
                        // xóa thằng trong data import
                        $keyWrong = [];
                        if (count($data_import)) {
                            foreach ($data_import as $key => $import) {
                                if ($import['person_cd'] == $person_cd) {
                                    $keyWrong[$key] = $person_cd;
                                    $dataExport[] = [
                                        'facility_cd' => $import['facility_cd'],
                                        'person_cd' => $person_cd,
                                        'error' => __('attendantcollectiveregistration.duplicate')
                                    ];
                                    break;
                                }
                            }
                            $data_import = array_diff_key($data_import, $keyWrong);
                        }
                        continue;
                    }
                    // 該当個人は別施設で登録済みです
                    if (count($t_convention_attendee_temp) > 0) {
                        foreach ($t_convention_attendee_temp as $item) {
                            $item = (object)$item;
                            if ($person_cd == $item->person_cd && $facility_cd != $item->facility_cd) {
                                $error++;
                                $errorMessage[] = __('attendantcollectiveregistration.person_exist');
                                $dataExport[] = [
                                    'facility_cd' => $facility_cd,
                                    'person_cd' => $person_cd,
                                    'error' => __('attendantcollectiveregistration.person_exist')
                                ];
                                break;
                            }
                        }
                    }

                    if ($error == 0) {
                        $data_import[] = [
                            "convention_id" => $convention_id,
                            "facility_cd" => $facility_cd,
                            "person_cd" => $person_cd,
                            "other_person_flag" => 0,
                            "facility_short_name" => $data_facility[$key_1]->facility_short_name ?? '',
                            "facility_name_kana" => $data_facility[$key_1]->facility_name_kana ?? '',
                            "person_name" => $data_person[$key_2]->person_name ?? '',
                            "person_name_kana" => $data_person[$key_2]->person_name_kana ?? '',
                            "department_cd" => $data_facility_person[$key_3]->department_cd ?? '',
                            "department_name" => $data_facility_person[$key_3]->department_name ?? '',
                            "display_position_cd" => $data_facility_person[$key_3]->display_position_cd ?? '',
                            "display_position_name" => $data_facility_person[$key_3]->display_position_name ?? '',
                        ];
                    }
                }
            }

            if(count($data_import) > 0){
                $this->service->import($data_import);
            }
            $data = [
                'total' => $total,
                'successNumber' => count($data_import),
                'errorNumber' => $total - count($data_import),
                'json_file_name' => $convention_id.'_'.$current.'.json'
            ];
            Storage::disk('public')->put(DIRECTORY_SEPARATOR.'exports'.DIRECTORY_SEPARATOR.$convention_id.'_'.$current.'.json', json_encode($dataExport));

            DB::commit();
            return $this->responseCreated(__('attendantcollectiveregistration.save'), $data);
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
            return $this->responseSystemError(__('attendantcollectiveregistration.system_error'));
        }
    }
}
