<?php

namespace App\Repositories\FacilityUser;

use App\Repositories\BaseRepository;
use App\Repositories\FacilityUser\FacilityUserRepositoryInterface;
use Illuminate\Support\Facades\DB;
use App\Services\SystemParameterService;
use App\Enums\LogBatchJob;

class FacilityUserRepository extends BaseRepository implements FacilityUserRepositoryInterface
{
    protected $system;
    public function __construct(SystemParameterService $system)
    {
        $this->system = $system;
    }
    /**
     * add master equipment history table h_facility_user
     * 
     * @author huynh
     */
    public function addMasterEquipmentHistoryPerson($jobId, $keyBatchJob)
    {
        $sql = "REPLACE INTO h_facility_user (facility_cd, user_cd, start_date, end_date, 
                main_flg, created_by, proxy_created_by, created_at, updated_by, proxy_updated_by, updated_at) 
                SELECT A.facility_cd , B.user_cd, B.start_date, B.end_date, B.main_flg, 
                    COALESCE('I03-B03') AS created_by,
                    COALESCE(NULL) AS proxy_created_by,
                    COALESCE('" . $this->system->getCurrentSystemDateTime() . "') AS created_at,
                    COALESCE('I03-B03') AS updated_by,
                    COALESCE(NULL) AS proxy_updated_by,
                    COALESCE('" . $this->system->getCurrentSystemDateTime() . "') AS updated_at
                FROM m_facility A 
                INNER JOIN h_area_user B ON A.prefecture_cd = B.prefecture_cd AND A.municipality_cd = B.municipality_cd";
        $this->_create($sql, [], LogBatchJob::TYPE_BATCH_JOB, $jobId, $keyBatchJob);
    }
    /**
     * add master equipment history person table h_facility_user
     * 
     * @author huynh
     */
    public function addMasterEquipmentHistoryPersonFromEnclave($jobId = null, $keyBatchJob = null)
    {
        $sql = "REPLACE INTO h_facility_user (facility_cd, user_cd, start_date, end_date, 
                main_flg, created_by, proxy_created_by, created_at, updated_by, proxy_updated_by, updated_at) 
                SELECT h_enclave_user.facility_cd , h_enclave_user.user_cd, h_enclave_user.start_date, h_enclave_user.end_date, h_enclave_user.main_flag, 
                    COALESCE('I03-B03') AS created_by,
                    COALESCE(NULL) AS proxy_created_by,
                    COALESCE('" . $this->system->getCurrentSystemDateTime() . "') AS created_at,
                    COALESCE('I03-B03') AS updated_by,
                    COALESCE(NULL) AS proxy_updated_by,
                    COALESCE('" . $this->system->getCurrentSystemDateTime() . "') AS updated_at
                FROM h_enclave_user
                JOIN m_facility ON m_facility.facility_cd = h_enclave_user.facility_cd
                WHERE h_enclave_user.main_flag = 0";
        $this->_create($sql, [], LogBatchJob::TYPE_BATCH_JOB, $jobId, $keyBatchJob);
    }
    /**
     * get all equipment person h_facility_user
     * 
     * @author huynh
     */
    public function getAllMasterEquipmentPerson()
    {
        $sql = "SELECT
        h_enclave_user.facility_cd,
        h_enclave_user.user_cd,
        h_enclave_user.start_date,
        h_enclave_user.end_date,
        (SELECT CONCAT('[', GROUP_CONCAT(JSON_OBJECT(
                        'facility_cd', h_facility_user.facility_cd,
                        'user_cd', h_facility_user.user_cd,
                        'start_date', h_facility_user.start_date,
                        'end_date', h_facility_user.end_date
                        )),']')
                        FROM h_facility_user
                        WHERE h_facility_user.main_flg = 1 AND h_facility_user.facility_cd = h_enclave_user.facility_cd
        ) AS facility_user
        FROM h_enclave_user
        JOIN m_facility ON m_facility.facility_cd = h_enclave_user.facility_cd
        WHERE main_flag = 1
        GROUP BY h_enclave_user.start_date, h_enclave_user.end_date, h_enclave_user.user_cd
        ORDER BY facility_cd, start_date ASC";
        return $this->_find($sql, []);
    }
    /**
     * update end_date table h_facility_user
     * 
     * @author huynh
     */
    public function updateMasterEquipmentPerson($data, $jobId, $keyBatchJob)
    {
        $sql = "INSERT INTO h_facility_user (facility_cd, user_cd, start_date, end_date, main_flg, created_by, created_at, updated_by, updated_at)
            VALUES :values;";
        $this->_bulkCreate($sql, $data, LogBatchJob::TYPE_BATCH_JOB, $jobId, $keyBatchJob);
    }
    /**
     * delete facility table h_facility_user
     * 
     * @author huynh
     */
    public function deleteMasterEquipmentPerson($facility_cd)
    {
        $sql = "DELETE FROM h_facility_user WHERE facility_cd = :facility_cd AND main_flg = 1";
        $this->_destroy($sql, ['facility_cd' => $facility_cd]);
    }
    /**
     * update again data table m_facility_user from table h_facility_user
     * 
     * @param date | date system or date setting by enter on terminal
     * @author huynh
     */
    public function updateUserFacilityMaster($date, $jobId, $keyBatchJob)
    {
        $sql = "REPLACE INTO m_facility_user (facility_cd, user_cd, main_flg, created_by, proxy_created_by, created_at, updated_by, proxy_updated_by, updated_at) 
        SELECT A.facility_cd, A.user_cd, A.main_flg,
                        COALESCE('I03-B03') AS created_by,
                        COALESCE(NULL) AS proxy_created_by,
                        COALESCE('" . date_format(date_create($this->system->getCurrentSystemDateTime()), 'YmdHis') . "') AS created_at,
                        COALESCE('I03-B03') AS updated_by,
                        COALESCE(NULL) AS proxy_updated_by,
                        COALESCE('" . date_format(date_create($this->system->getCurrentSystemDateTime()), 'YmdHis') . "') AS updated_at
        FROM h_facility_user A 
        INNER JOIN m_facility B ON A.facility_cd = B.facility_cd 
        WHERE '$date' BETWEEN A.start_date AND A.end_date";
        return $this->_create($sql, [], LogBatchJob::TYPE_BATCH_JOB, $jobId, $keyBatchJob);
    }
    /**
     * empty table
     * 
     * @author huynh
     */
    public function emptyTable($table, $jobId, $keyBatchJob)
    {
        $sql = "DELETE FROM $table";
        $this->_destroy($sql, [], LogBatchJob::TYPE_BATCH_JOB, $jobId, $keyBatchJob);
    }
    /**
     * get list attendee [C01-P01] 出席者一覧出力 
     * task : BU5_MSFA-620
     * @param userCd : id of user
     * 
     * @author huynh
     */
    public function listAttendee($conditions)
    {
        $sql = "SELECT
        m_org.org_label,
        m_facility_user.user_cd,
        m_user.user_name,
        t_convention_attendee.facility_cd,
        t_convention_attendee.facility_short_name,
        t_convention_attendee.department_cd,
        t_convention_attendee.department_name,
        t_convention_attendee.display_position_name,
        m_medical_staff.medical_staff_name,
        t_convention_attendee.person_cd,
        t_convention_attendee.person_name,
        m_variable_definition.definition_label,
        t_schedule.start_date,
        t_convention.convention_id,
        t_convention_attendee.other_person_flag,
        (SELECT CONCAT('[', GROUP_CONCAT(JSON_OBJECT(
                'status_item_cd', t_convention_attendee_status_detail.status_item_cd,
                'status_item_value', t_convention_attendee_status_detail.status_item_value
                )),']')
                FROM t_convention_attendee_status_detail
                WHERE t_convention_attendee_status_detail.convention_attendee_id = t_convention_attendee.convention_attendee_id
        ) AS convention_attendee_status_detail,
        (
            SELECT
                CONCAT('[', GROUP_CONCAT(JSON_OBJECT(
                    'start_date', t_schedule.start_time
                )),']')
            FROM t_schedule
            JOIN t_visit ON t_visit.schedule_id = t_schedule.schedule_id
            JOIN t_call ON t_call.visit_id = t_visit.visit_id
            WHERE CAST(t_schedule.start_date AS DATE) >= :convention_start_date_sub AND CAST(t_schedule.start_date AS DATE) <= :convention_start_date_add_two_day_sub
            AND t_call.person_cd = t_convention_attendee.person_cd AND t_schedule.schedule_type = 10
        ) AS follow_dates,
        (
            SELECT
                CONCAT('[', GROUP_CONCAT(JSON_OBJECT(
                    'start_date', t_schedule.start_date,
                    'person_cd', t_convention_attendee.person_cd,
                    'convention_id', t_convention_sub.convention_id,
                    'convention_attendees', (
                                                SELECT count(*) FROM t_convention_attendee as t_convention_attendee_sub
                                                WHERE t_convention_attendee_sub.convention_id = t_convention_sub.convention_id
                                                AND t_convention_attendee_sub.person_cd = t_convention_attendee.person_cd
                                                AND t_convention_attendee_sub.other_person_flag = 0
                    ),
                    'status_item_value_200', (
                            SELECT count(*) FROM t_convention_attendee as t_convention_attendee_sub
                            JOIN t_convention_attendee_status_detail as t_convention_attendee_status_detail_sub
                            ON t_convention_attendee_status_detail_sub.convention_attendee_id = t_convention_attendee_sub.convention_attendee_id
                            WHERE t_convention_attendee_sub.convention_attendee_id = t_convention_attendee_status_detail_sub.convention_attendee_id
                            AND t_convention_attendee_sub.person_cd = t_convention_attendee.person_cd
                            AND t_convention_attendee_sub.other_person_flag = 0
                            AND status_item_cd = :status_item_cd_200
                            AND status_item_cd = 1
                    ),
                    'status_item_value_700', (
                            SELECT count(*) FROM t_convention_attendee as t_convention_attendee_sub
                            JOIN t_convention_attendee_status_detail as t_convention_attendee_status_detail_sub
                            ON t_convention_attendee_status_detail_sub.convention_attendee_id = t_convention_attendee_sub.convention_attendee_id
                            WHERE t_convention_attendee_sub.convention_attendee_id = t_convention_attendee_status_detail_sub.convention_attendee_id
                            AND t_convention_attendee_sub.person_cd = t_convention_attendee.person_cd
                            AND t_convention_attendee_sub.other_person_flag = 0
                            AND status_item_cd = :status_item_cd_700
                            AND status_item_value = 1
                    )
                )),']')
            FROM t_convention as t_convention_sub
            JOIN t_schedule ON t_schedule.schedule_id = t_convention_sub.schedule_id
            WHERE t_convention.series_convention_id = t_convention_sub.series_convention_id
        ) AS series_convention
        FROM m_facility_user
        JOIN m_user ON m_user.user_cd = m_facility_user.user_cd
        JOIN m_user_org ON m_user_org.user_cd = m_facility_user.user_cd AND m_user_org.main_flag = 1
        JOIN m_org ON m_org.org_cd = m_user_org.org_cd
        JOIN t_convention_attendee ON t_convention_attendee.facility_cd = m_facility_user.facility_cd
        LEFT JOIN m_medical_staff ON m_medical_staff.medical_staff_cd = t_convention_attendee.other_medical_staff_type
        JOIN t_convention ON t_convention.convention_id = t_convention_attendee.convention_id
        JOIN t_schedule ON t_schedule.schedule_id = t_convention.schedule_id
        LEFT JOIN m_variable_definition ON m_variable_definition.definition_value = t_convention_attendee.part_type AND m_variable_definition.definition_name = :definition_name";
        //出席予定者のみ
        if ($conditions->attendance != 0) {
            $sql .= " JOIN t_convention_attendee_status_detail ON t_convention_attendee_status_detail.convention_attendee_id = t_convention_attendee.convention_attendee_id
                        AND t_convention_attendee_status_detail.status_item_cd = '300' 
                        AND t_convention_attendee_status_detail.status_item_value = '10'";
        }
        $sql .= " WHERE t_convention_attendee.convention_id = :convention_id";
        //未フォローのみ
        if ($conditions->unfollow != 0) {
            $sql .= " AND (SELECT
                CONCAT('[', GROUP_CONCAT(JSON_OBJECT(
                    'start_date', t_schedule.start_date
                )),']')
                FROM t_schedule
                JOIN t_visit ON t_visit.schedule_id = t_schedule.schedule_id
                JOIN t_call ON t_call.visit_id = t_visit.visit_id
                WHERE CAST(t_schedule.start_date AS DATE) >= :convention_start_date AND CAST(t_schedule.start_date AS DATE) <= :convention_start_date_add_two_day
                AND t_call.person_cd = t_convention_attendee.person_cd) IS NULL";
            $condition['convention_start_date'] = $conditions->convention_start_date;
            $condition['convention_start_date_add_two_day'] = $conditions->convention_start_date_add_two_day;
        }
        $condition['convention_id'] = $conditions->convention_id;
        if (!empty($conditions->user_cd)) {
            $sql .= " AND m_facility_user.user_cd IN " . $this->_buildCommaString($conditions->user_cd, true);
        }
        $sql .= " GROUP BY t_convention_attendee.convention_attendee_id
                ORDER BY
                    t_convention_attendee.facility_name_kana,
                    t_convention_attendee.department_cd,
                    t_convention_attendee.display_position_cd,
                    t_convention_attendee.person_name_kana";
        $condition['convention_start_date_sub'] = $conditions->convention_start_date;
        $condition['convention_start_date_add_two_day_sub'] = $conditions->convention_start_date_add_two_day;
        $condition['status_item_cd_200'] = $conditions->status_item_cd_200;
        $condition['status_item_cd_700'] = $conditions->status_item_cd_700;
        $condition['definition_name'] = ROLE_CLASSIFICATION;
        return $this->_find($sql, $condition);
    }
    /**
     * get exchange data retention time from table c_system_parameter
     * 
     * @param text : string data
     * @author huynh
     */
    public function getExchangeDataRetentionTime($text)
    {
        $sql = "select * from c_system_parameter where parameter_key = :parameter_key";
        return DB::select($sql, [
            'parameter_key' => $text
        ]);
    }
    /**
     * 2. delete exchange data retention time\
     * @param dateCompare : time setting to delete
     * 
     * @author huynh 
     */
    public function deleteExchangeDataRetentionTime($dateCompare, $jobId, $keyBatchJob)
    {
        $sql2 = "DELETE tsch, tvis, tcall, tdet, tdedo, trep, trede, treva, tapre, taprede, tacus 
                FROM t_schedule tsch  
                LEFT JOIN t_visit tvis ON tvis.schedule_id = tsch.schedule_id 
                LEFT JOIN t_call tcall ON tcall.visit_id = tvis.visit_id 
                LEFT JOIN t_detail tdet ON tdet.call_id = tcall.call_id 
                LEFT JOIN t_detail_document tdedo ON tdedo.detail_id = tdet.detail_id 
                LEFT JOIN t_report_detail trede ON trede.report_detail_id = tsch.schedule_id 
                LEFT JOIN t_report trep ON trep.report_id = trede.report_id 
                LEFT JOIN t_report_vacation treva ON treva.report_id = trep.report_id 
                LEFT JOIN t_approval_request tapre ON tapre.request_target_id = trep.report_id 
                LEFT JOIN t_approval_request_detail taprede ON taprede.request_id = tapre.request_id 
                LEFT JOIN t_accompanying_user tacus ON tacus.visit_id = tvis.visit_id 
                WHERE tsch.start_date < '$dateCompare' AND tsch.schedule_type = (SELECT definition_value FROM m_variable_definition WHERE definition_name='スケジュール区分' AND definition_label='面談' LIMIT 1)"; // t_call
        $this->_destroy($sql2, [], LogBatchJob::TYPE_BATCH_JOB, $jobId, $keyBatchJob);
    }
    /**
     * 3. delete tutorial data
     * @param dateCompare : time setting to delete
     * 
     * @author huynh 
     */
    public function deleteDataTutorial($dateCompare, $jobId, $keyBatchJob)
    {
        $sql2 = "DELETE tsch, tbri, tbrat, tbrpr, tbrdo, tbrexde, tbratco  
                FROM t_schedule tsch  
                LEFT JOIN t_briefing tbri ON tbri.schedule_id = tsch.schedule_id 
                LEFT JOIN t_briefing_attendee tbrat ON tbrat.briefing_id = tbri.briefing_id 
                LEFT JOIN t_briefing_product tbrpr ON tbrpr.briefing_id = tbri.briefing_id 
                LEFT JOIN t_briefing_document tbrdo ON tbrdo.briefing_id = tbri.briefing_id 
                LEFT JOIN t_briefing_expense_detail tbrexde ON tbrexde.briefing_id = tbri.briefing_id 
                LEFT JOIN t_briefing_attendee_count tbratco ON tbratco.briefing_id = tbri.briefing_id 
                WHERE tsch.start_date < '$dateCompare' AND tsch.schedule_type = (SELECT definition_value FROM m_variable_definition WHERE definition_name='スケジュール区分' AND definition_label='説明会' LIMIT 1)";
        $this->_destroy($sql2, [], LogBatchJob::TYPE_BATCH_JOB, $jobId, $keyBatchJob);
    }
    /**
     * 4. delete present data
     * @param dateCompare : time setting to delete
     * 
     * @author huynh 
     */
    public function deleteDataPresent($dateCompare, $jobId, $keyBatchJob)
    {
        $sql2 = "DELETE tsch, tco, tcopr, tcodo, tcota, tcofi, tcoat, tcoatstde, tcoothe, tcoexde, tsccosu, tfi 
                FROM t_schedule tsch  
                LEFT JOIN t_schedule_common_subject tsccosu ON tsccosu.schedule_id = tsch.schedule_id 
                LEFT JOIN t_convention tco ON tco.schedule_id = tsch.schedule_id 
                LEFT JOIN t_convention_product tcopr ON tcopr.convention_id = tco.convention_id 
                LEFT JOIN t_convention_document tcodo ON tcodo.convention_id = tco.convention_id 
                LEFT JOIN t_convention_target_org tcota ON tcota.convention_id = tco.convention_id 
                LEFT JOIN t_convention_file tcofi ON tcofi.convention_id = tco.convention_id 
                LEFT JOIN t_convention_attendee tcoat ON tcoat.convention_id = tco.convention_id 
                LEFT JOIN t_convention_attendee_status_detail tcoatstde ON tcoatstde.convention_attendee_id = tcoat.convention_attendee_id  
                LEFT JOIN t_convention_other_headcount tcoothe ON tcoothe.convention_id = tco.convention_id 
                LEFT JOIN t_convention_expense_detail tcoexde ON tcoexde.convention_id = tco.convention_id 
                LEFT JOIN t_file tfi ON tfi.file_id = tcofi.file_id 
                WHERE tsch.start_date < '$dateCompare' AND tsch.schedule_type  = (SELECT definition_value FROM m_variable_definition WHERE definition_name='スケジュール区分' AND definition_label='講演会' LIMIT 1)";
        $this->_destroy($sql2, [], LogBatchJob::TYPE_BATCH_JOB, $jobId, $keyBatchJob);
    }
    /**
     * 5. delete Other Event data
     * @param dateCompare : time setting to delete
     * 
     * @author huynh 
     */
    public function deleteDataOtherEvent($dateCompare, $jobId, $keyBatchJob)
    {
        $sql2 = "DELETE tsch, tofsc, tpri, tsccosu 
                FROM t_schedule tsch  
                LEFT JOIN t_office_schedule tofsc ON tofsc.schedule_id = tsch.schedule_id 
                LEFT JOIN t_private tpri ON tpri.schedule_id = tsch.schedule_id 
                LEFT JOIN t_schedule_common_subject tsccosu ON tsccosu.schedule_id = tsch.schedule_id 
                WHERE tsch.start_date < '$dateCompare' 
                AND tsch.schedule_type  IN (SELECT definition_value FROM m_variable_definition WHERE definition_name='スケジュール区分' AND definition_label='プライベート' OR definition_label='社内予定')";
        // AND tpri.schedule_id IN (SELECT schedule_id FROM t_schedule WHERE t_schedule.schedule_type = (SELECT definition_value FROM m_variable_definition WHERE definition_name='スケジュール区分' AND definition_label='プライベート' LIMIT 1)) 
        // AND tofsc.schedule_id IN (SELECT schedule_id FROM t_schedule WHERE t_schedule.schedule_type = (SELECT definition_value FROM m_variable_definition WHERE definition_name='スケジュール区分' AND definition_label='プライベート' LIMIT 1))";
        $this->_destroy($sql2, [], LogBatchJob::TYPE_BATCH_JOB, $jobId, $keyBatchJob);
    }

    /**
     * 6. delete message data
     * @param dateCompare : time setting to delete
     * 
     * @author huynh 
     */
    public function deleteDataMessage($dateCompare, $jobId, $keyBatchJob)
    {
        $sql2 = "DELETE tme, tmere 
                FROM t_message tme  
                LEFT JOIN t_message_read tmere ON tmere.message_id = tme.message_id 
                WHERE tme.created_at < '$dateCompare'";
        $this->_destroy($sql2, [], LogBatchJob::TYPE_BATCH_JOB, $jobId, $keyBatchJob);
    }

    /**
     * 7. delete notification data
     * @param dateCompare : time setting to delete
     * 
     * @author huynh 
     */
    public function deleteDataNotification($dateCompare, $jobId, $keyBatchJob)
    {
        $sql2 = "DELETE tin, tinpa 
                FROM t_inform tin   
                LEFT JOIN t_inform_parameter tinpa ON tinpa.inform_id = tin.inform_id  
                WHERE tin.created_at < '$dateCompare'";
        $this->_destroy($sql2, [], LogBatchJob::TYPE_BATCH_JOB, $jobId, $keyBatchJob);
    }

    /**
     * 8. delete stock data
     * @param dateCompare : time setting to delete
     * 
     * @author huynh 
     */
    public function deleteDataStock($dateCompare, $jobId, $keyBatchJob)
    {
        $sql2 = "DELETE tst, tstdo, tstdpro    
                FROM t_stock tst  
                LEFT JOIN t_stock_document tstdo ON tstdo.stock_id = tst.stock_id 
                LEFT JOIN t_stock_product tstdpro ON tstdpro.stock_id = tst.stock_id 
                WHERE tst.created_at < '$dateCompare' 
                AND tst.status_type  = (SELECT definition_value FROM m_variable_definition WHERE definition_name='ストックステータス' AND definition_label='計画済' LIMIT 1)";
        $this->_destroy($sql2, [], LogBatchJob::TYPE_BATCH_JOB, $jobId, $keyBatchJob);
    }

    /**
     * 9. delete operation log data
     * @param dateCompare : time setting to delete
     * 
     * @author huynh 
     */
    public function deleteDataOperationLog($dateCompare, $jobId, $keyBatchJob)
    {
        $sql2 = "DELETE FROM z_access    
                WHERE created_at < '$dateCompare'";
        $this->_destroy($sql2, [], LogBatchJob::TYPE_BATCH_JOB, $jobId, $keyBatchJob);
    }
    /**
     * Out Put data before delete 
     */
    public function outputDataToFileOperationLog($dateCompare, $jobId, $keyBatchJob)
    {
        $folder = public_path(config('variables.out_put_file_transaction'));
        $nameFile = date_format(date_create($this->system->getCurrentSystemDateTime()), 'YmdHis') . '_' . bin2hex(random_bytes(10));
        $file = $folder . '/' . $nameFile . '.csv';
        if (!file_exists($folder) && !is_dir($folder)) {
            mkdir($folder, 0777, true);
        }
        $sql = "SELECT * FROM z_access 
        WHERE created_at < '$dateCompare' 
        INTO OUTFILE '$file' 
        FIELDS TERMINATED BY ',' 
        ENCLOSED BY '\"' 
        ESCAPED BY '' 
        LINES TERMINATED BY '\n'";
        // run sql to output file
        DB::select($sql);
        // create file zip
        $zip = new \ZipArchive();
        $zip->open($nameFile . '.zip', \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
        $zip->addFile($file, $nameFile. '.csv');
        $zip->close();
        // delete file sql
        unlink($file);
        // move file zip to folder
        rename(base_path() . '/' . $nameFile. '.zip', $folder . '/' . $nameFile. '.zip');
    }
    /**
     * get convention name for name export
     */
    public function getConventionName($idConvention)
    {
        $sql = "SELECT convention_name FROM t_convention WHERE convention_id = :convention_id";
        return $this->_first($sql, [
            'convention_id' => $idConvention
        ]);
    }
}
