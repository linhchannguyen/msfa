<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\AttendantBulkRegistrationErrorContentOutput\ExportCsvRequest;
use App\Services\AttendantCollectiveRegistrationService;
use Exception;
use App\Services\SystemParameterService;
use Illuminate\Support\Facades\Storage;


class AttendantBulkRegistrationErrorContentOutputController extends Controller
{
    private $service;

    public function __construct(AttendantCollectiveRegistrationService $service, SystemParameterService $system)
    {
        $this->service = $service;
        $this->system = $system;
        $this->middleware('role.has:' . config('role.CONVENTION_ATTENDEE_MG.CODE') . ',' . config('role.SYS_MG.CODE'));
    }

    /**
     *  @OA\POST(
     *      path="/api/attendant-bulk-registration-error-content-output/export-csv",
     *      operationId="exportCsv",
     *      tags={"C01-P02"},
     *      summary="Attendant Bulk Registration Error Content Output",
     *      description="Attendant Bulk Registration Error Content Output",
     *      security={{"jwt": {}}},
     *      @OA\RequestBody(
     *          description="Export Csv params",
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
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
    public function exportCsv(ExportCsvRequest $request)
    {
        try {
            $json_file_name = $request->json_file_name;
            $json = Storage::disk('public')->get(DIRECTORY_SEPARATOR.'exports'.DIRECTORY_SEPARATOR.$json_file_name);
            $dataExport = json_decode($json, true);
            $date_system = date_format(date_create($this->system->getCurrentSystemDateTime()), 'Ymd');
            $file_name = 'エラー内容_' . $date_system . '.csv';
            $col_title = array("施設コード", "個人コード", "エラー内容");
            $col_value = array('facility_cd', 'person_cd', 'error');
            $headers = array(
                "Content-Type" => "application/csv",
                "Content-Disposition" => "attachment; filename=" . $file_name,
                "Content-Type" => "application/csv; charset=Shift_JIS",
                "Cache-Control" => "max-age=0",
            );
            $callback = function () use ($col_title, $col_value, $dataExport) {
                $data = array();
                // Title
                if (isset($col_title)) {
                    $data_detail = array();
                    foreach ($col_title as $value_title) {
                        $value_title = str_replace(array("\r\n", "\n\r", "\n", "\r", '"'), '', $value_title);
                        array_push(
                            $data_detail,
                            mb_convert_encoding($value_title, "SJIS-win", 'auto') // Convert data to shift-JIS
                        );
                    }
                    array_push(
                        $data,
                        $data_detail
                    );
                }
                // Data
                foreach ($dataExport as $value) {
                    $data_detail = array();
                    foreach ($col_value as $value_title) {
                        if ($value_title != null && $value_title != "") {
                            $value_data = str_replace(array("\r\n", "\n\r", "\n", "\r", '"', 'amp;'), '', $value[$value_title]);
                            $value_data = html_entity_decode($value_data);
                        } else {
                            $value_data = "";
                        }
                        array_push(
                            $data_detail,
                            mb_convert_encoding($value_data, "SJIS-win", 'auto') // Convert data to shift-JIS
                        );
                    }
                    array_push(
                        $data,
                        $data_detail
                    );
                }
                $stream = fopen('php://output', 'w');
                foreach ($data as $row) {
                    fputcsv($stream, $row);
                }
                fclose($stream);
            };
            // Storage::disk('public')->delete(DIRECTORY_SEPARATOR.'exports'.DIRECTORY_SEPARATOR.$json_file_name);
            return response()->stream($callback, 200, $headers);
        } catch (Exception $e) {
            throw $e;
            return $this->responseSystemError(__('attendantcollectiveregistration.system_error'));
        }
    }
}
