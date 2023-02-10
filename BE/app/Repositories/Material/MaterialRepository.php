<?php

namespace App\Repositories\Material;

use Illuminate\Support\Facades\DB;
use App\Repositories\BaseRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Repositories\Material\MaterialRepositoryInterface;
use App\Traits\StringConvertTrait;
use App\Traits\DateTimeTrait;

class MaterialRepository extends BaseRepository implements MaterialRepositoryInterface
{
    use StringConvertTrait, DateTimeTrait;
    private $date, $document_type;
    public function __construct()
    {
        $this->date = $this->currentJapanDateTime('Y-m-d H:i:s');
        $this->document_type = "資材区分";
        $this->document_options = "有無選択肢";
    }

    public function allMaterial()
    {
        $query['document_type'] = $this->document_type;
        $sql = "SELECT definition_label as document_label, definition_value as document_type FROM m_variable_definition WHERE definition_name = :document_type";
        return $this->_find($sql, $query);
    }

    public function allMedicalSubjects()
    {
        $sql = "SELECT medical_subjects_group_cd, medical_subjects_group_name FROM m_medical_subjects_group";
        return $this->_all($sql);
    }

    public function allSafetyInformation()
    {
        $query['document_options'] = $this->document_options;
        $sql = "SELECT definition_label as safety_information_label, definition_value as safety_information_flag FROM m_variable_definition WHERE definition_name = :document_options";
        return $this->_find($sql, $query);
    }

    public function allOffLabelInformation()
    {
        $query['document_options'] = $this->document_options;
        $sql = "SELECT definition_label as off_label_information_label, definition_value as off_label_information_flag FROM m_variable_definition WHERE definition_name = :document_options";
        return $this->_find($sql, $query);
    }

    public function allDataFilter($data)
    {
        $where = "";
        $join = "";
        $sub_select = "";
        $document_id = [];
        $query = [];
        if ($data->document_id) {
            $document_id = explode(',', $data->document_id);
        }
        // filter document_id
        if (count($document_id) > 0) {
            $where .= " AND T7.document_id IN " . $this->_buildCommaString($document_id, true);
        } else {
            if ($data->document_type == 10) {
                $join .= " JOIN t_original_document_detail T2 ON T1.document_id = T2.document_id";
                $sql_org_user = "SELECT T3.org_cd,T3.layer_num,layer_1 
                            FROM m_user T1 
                                JOIN m_user_org T2 ON T1.user_cd = T2.user_cd 
                                JOIN m_org T3 ON T2.org_cd = T3.org_cd
                            WHERE T1.user_cd = :user_cd
                            GROUP BY layer_1";
                $org_user = $this->_find($sql_org_user, ['user_cd' => $data->user_login]) ?? [];
                $where_org = "";
                if (count($org_user) > 0) {
                    for ($i = 0; $i < count($org_user); $i++) {
                        if ($i == 0) {
                            $where_org .= ' layer_' . $org_user[$i]->layer_num . ' = ' . $org_user[$i]->org_cd;
                        } else {
                            $where_org .= ' OR layer_' . $org_user[$i]->layer_num . ' = ' . $org_user[$i]->org_cd;
                        }
                        if ($i == (count($org_user) - 1)) {
                            $where_org .= ')';
                        }
                    }
                }
                $where .= " AND ((CASE WHEN T2.available_org_cd IS NULL THEN (T2.available_org_cd IS NULL) ELSE T2.available_org_cd IN ( SELECT org_cd FROM m_org WHERE (" . $where_org . ") END) OR T7.create_user_cd = :create_user_cd)";
                $sub_select = " ,T2.document_version,T2.available_org_cd";
                $query['create_user_cd'] = $data->user_login;
                // get document children
                if ($data->subMaterialSelectableFlag) {
                    $where .= " AND T1.document_id = T2.document_id";
                } elseif (!$data->subMaterialSelectableFlag) {
                    $where .= " AND T1.document_id = T2.parent_document_id";
                }
            } elseif ($data->document_type == 20) {
                $join .= " JOIN t_customize_document_detail T2 ON T1.document_id = T2.document_id";
                $where .= " AND T7.create_user_cd = :create_user_cd";
                $query['create_user_cd'] = $data->user_login;
            }
            if ($data->document_type) {
                $where .= " AND T7.document_type = :document_type";
                $query['document_type'] = $data->document_type;
            }
            // filter A3.product_cd != ""
            if ($data->product_cd) {
                $where .= " AND T5.product_cd = :product_cd";
                $query['product_cd'] = $data->product_cd;
            }
            // filter A4.medical_subjects_group_cd != ""
            if ($data->medical_subjects_group_cd) {
                $where .= " AND T7.medical_subjects_group_cd = :medical_subjects_group_cd";
                $query['medical_subjects_group_cd'] = $data->medical_subjects_group_cd;
            }
            // filter A5.safety_information_flag != ""
            if ((string)$data->safety_information_flag !== "") {
                $where .= " AND T7.safety_information_flag = :safety_information_flag";
                $query['safety_information_flag'] = $data->safety_information_flag;
            }
            // filter A6.off_label_information_flag == 10
            if ((string)$data->off_label_information_flag !== "") {
                $where .= " AND T7.off_label_information_flag = :off_label_information_flag";
                $query['off_label_information_flag'] = $data->off_label_information_flag;
            }
            // filter A6.date
            if ($data->date) {
                $where .= " AND T7.start_date <= :start_date AND T7.end_date >= :end_date";
                $query['start_date'] = $data->date;
                $query['end_date'] = $data->date;
            }
            // filter materialName
            if ($data->document_name) {
                $where .= " AND T7.document_name like :document_name";
                $query['document_name'] = "%" . trim($this->convert_kana($data->document_name)) . "%";
            }
        }
        $sql = "SELECT  T1.document_id,
                    T7.file_type,
                    T7.document_type,
                    T7.document_name,
                    T7.start_date,
                    T7.end_date,
                    T7.disease,
                    T5.product_name,
                    T7.safety_information_flag,
                    T7.off_label_information_flag,
                    T6.medical_subjects_group_name" . $sub_select . "
                FROM t_document T1 
                    " . $join . "
                    JOIN t_document T7 ON T7.document_id =  T1.document_id
                    LEFT JOIN t_document_product T4 ON T7.document_id = T4.document_id
                    LEFT JOIN m_product T5 ON T5.product_cd = T4.product_cd 
                    LEFT JOIN m_medical_subjects_group T6 ON T7.medical_subjects_group_cd = T6.medical_subjects_group_cd
                WHERE 1 = 1 " . $where . " GROUP BY T7.document_id";
        return $this->_find($sql, $query);
    }

    public function getOrgDocuemnt($org_cd)
    {
        $sql_org_detail = "SELECT * FROM m_org WHERE org_cd = :org_cd";
        $org_detail = $this->_first($sql_org_detail, ['org_cd' => $org_cd]);
        if (!isset($org_detail->org_cd)) {
            return [];
        }
        $sql_list_org_origin_and_children = "SELECT CONCAT(A.list_org,',',A.layer) as list_org from (select GROUP_CONCAT(org_cd) as list_org,CASE WHEN layer_num = 1 THEN layer_1
                                            WHEN layer_num = 2 THEN CONCAT(layer_1)
                                            WHEN layer_num = 3 THEN CONCAT(layer_1,',',layer_2)
                                            WHEN layer_num = 4 THEN CONCAT(layer_1,',',layer_2,',',layer_3)
                                            WHEN layer_num = 5 THEN CONCAT(layer_1,',',layer_2,',',layer_3,',',layer_4)
                                            END layer
                                            FROM m_org WHERE layer_" . $org_detail->layer_num . " = :org_cd) A";
        return $this->_first($sql_list_org_origin_and_children, ['org_cd' => $org_cd]);
    }
}
