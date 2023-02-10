<?php

namespace App\Repositories\DocumentPowerPoint;

use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;
use App\Traits\DateTimeTrait;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Enums\Document;
use App\Traits\Base64StringFileTrait;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;

class DocumentPowerPointRepository extends BaseRepository implements DocumentPowerPointRepositoryInterface
{
    use DateTimeTrait, Base64StringFileTrait;
    private $date;
    protected $useAutoMeta = true;
    public function __construct()
    {
        $this->date = $this->currentJapanDateTime('Y-m-d H:i:s');
    }
    /**
     * get List document original
     *
     * @author huynh
     */
    public function getListOriginalDocument () {
        $sql = "SELECT T2.document_name, T1.document_id, T1.parent_document_id
                FROM t_original_document_detail T1
                JOIN t_document T2 ON T1.parent_document_id = T2.document_id";
        return $this->_find($sql, []);
    }

    /**
     * get list item of a original document
     *
     * @author huynh
     */
    public function getContentOriginalDocument ($idOriginal) {
        // $urlAws = config('filesystems.disks.public.url');
        $sql = "SELECT T2.document_name, T2.file_type, T1.original_document_id, T1.original_document_page_num,
                (
                    CASE T2.file_type
                    WHEN 10 THEN (CONCAT('document/original/doc/', T1.original_document_id, '/' ,T1.original_document_page_num, '/', 'normal.jpg'))
                    ELSE (CONCAT('document/original/video/', T1.original_document_id, '/', 'normal.jpg'))
                    END
                ) AS thumbnail,
                T6.cover_flag,
                (SELECT GROUP_CONCAT(T5.document_usage_id) FROM t_document_usage_situation T5 WHERE T5.document_id = T2.document_id) AS list_document_usage_id,
                (
                    CASE T2.file_type
                    WHEN 10 THEN (CONCAT('document/original/doc/',T1.original_document_id,'/',T1.original_document_page_num, '/', T1.original_document_page_num, '.pdf'))
                    ELSE (CONCAT('document/original/video/',T1.original_document_id, '/' ,T1.original_document_id, '.mp4'))
                    END
                ) AS file_url,
                T3.available_org_cd
                FROM t_original_document_file_page T1
                JOIN t_original_document_detail T3 ON T3.document_id = T1.original_document_id
                JOIN t_document T2 ON T2.document_id = T1.original_document_id
                LEFT JOIN t_file T4 ON T4.file_id = T1.file_url
                LEFT JOIN m_document_category T6 ON T6.document_category_cd = T3.document_category_cd
                WHERE T1.original_document_id = :original_document_id";
        return $this->_find($sql, [
            // 'url_aws_1' => $urlAws,
            // 'url_aws_2' => $urlAws,
            // 'url_aws_3' => $urlAws,
            // 'url_aws_4' => $urlAws,
            'original_document_id' => $idOriginal
        ]);
    }
    public function listSlideShow ($idDocument) {
        if ( $this->isDocumentCustom($idDocument) ) {
            $customDocumentSlide = $this->listDocumentCustom($idDocument);
            // check if thumbnail not exist then thumbnail value url document original
            foreach ($customDocumentSlide as &$customDSlide) {
                $customDSlide->thumbnail = $this->encodeBase64String($customDSlide->thumbnail);
                $customDSlide->file_url = $this->encodeBase64String($customDSlide->file_url);
                $convertCoverJsonToArray = @json_decode(base64_decode($customDSlide->cover_html), true);
                if ( $convertCoverJsonToArray && count($convertCoverJsonToArray) ) {
                    if ( isset($convertCoverJsonToArray[0]['thumbnail']) ) {
                        $thumbnailCustom = $convertCoverJsonToArray[0]['thumbnail'];
                        $urlPathThumbnailCustom = $this->decodeStringBase64(str_replace(config('filesystems.disks.public_server.url') . '/', '', $thumbnailCustom));
                        if (!File::exists($urlPathThumbnailCustom)) {
                            $convertCoverJsonToArray[0]['thumbnail'] = $customDSlide->thumbnail;
                            $customDSlide->cover_html = base64_encode(json_encode($convertCoverJsonToArray));
                        }
                    }
                }
            }
            return $customDocumentSlide;
        }
        $customDocumentSlide = $this->getContentOriginalDocumentForSlideshow($idDocument);
        // check if thumbnail not exist then thumbnail value url document original
        foreach ($customDocumentSlide as &$customDSlide) {
            $customDSlide->thumbnail = $this->encodeBase64String($customDSlide->thumbnail);
            $customDSlide->file_url = $this->encodeBase64String($customDSlide->file_url);
        }
        return $customDocumentSlide;
    }
    public function infoDocument ($idDocument) {
        $sql = "SELECT * FROM t_document WHERE document_id = :document_id";
        return $this->_first($sql, [
            'document_id' => $idDocument
        ]);
    }
    public function getContentOriginalDocumentForSlideshow ($idOriginal) {
        // $urlAws = config('filesystems.disks.s3.url');
        $sql = "SELECT T2.document_id, T2.document_name, T2.file_type, T1.original_document_id, T1.original_document_page_num,T1.original_document_page_num AS page_num, '' AS cover_html,
                (
                    CASE T2.file_type
                    WHEN 10 THEN (CONCAT('document/original/doc/', T1.original_document_id, '/' ,T1.original_document_page_num, '/', 'normal.jpg'))
                    ELSE (CONCAT( 'document/original/video/', T1.original_document_id, '/', 'normal.jpg'))
                    END
                ) AS thumbnail,
                -- (CONCAT( :url_aws_1,'/document/original/', (CASE T2.file_type WHEN 10 THEN 'doc' ELSE 'video' END),'/', T1.original_document_id, '/' ,T1.original_document_page_num, '/', 'normal.jpg')) AS thumbnail,
                -- T1.file_url,
                T6.cover_flag,
                (SELECT GROUP_CONCAT(T5.document_usage_id) FROM t_document_usage_situation T5 WHERE T5.document_id = T2.document_id) AS list_document_usage_id,
                (
                    CASE T2.file_type
                    WHEN 10 THEN (CONCAT('document/original/doc/',T1.original_document_id,'/',T1.original_document_page_num, '/', T1.original_document_page_num, '.pdf'))
                    ELSE (CONCAT( 'document/original/video/',T1.original_document_id, '/' ,T1.original_document_id, '.mp4'))
                    END
                ) AS file_url
                -- (CONCAT( :url_aws_3,'/document/original/', (CASE T2.file_type WHEN 10 THEN 'doc' ELSE 'video' END),'/',T1.original_document_id, '/' ,T1.original_document_page_num, '/', T1.original_document_page_num, (CASE T2.file_type WHEN 10 THEN '.pdf' ELSE '.mp4' END))) AS file_url
                -- T4.file_url AS thumbnail
                FROM t_original_document_file_page T1
                JOIN t_original_document_detail T3 ON T3.document_id = T1.original_document_id
                JOIN t_document T2 ON T2.document_id = T1.original_document_id
                LEFT JOIN t_file T4 ON T4.file_id = T1.file_url
                -- LEFT JOIN t_file T4 ON T4.file_id = T3.file_id
                LEFT JOIN m_document_category T6 ON T6.document_category_cd = T3.document_category_cd
                -- LEFT JOIN t_document_usage_situation T5 ON T5.document_id = T2.document_id
                WHERE T1.original_document_id = :original_document_id";
        return $this->_find($sql, [
            // 'url_aws_1' => $urlAws,
            // 'url_aws_2' => $urlAws,
            // 'url_aws_3' => $urlAws,
            // 'url_aws_4' => $urlAws,
            'original_document_id' => $idOriginal
        ]);
    }
    private function isDocumentCustom ($idDocument) {
        $sql = "SELECT M1.definition_value FROM m_variable_definition M1
        JOIN t_document T1 ON T1.document_type = M1.definition_value
        WHERE M1.definition_label='カスタム資材' AND T1.document_id = :document_id";
        $data = DB::select($sql, ['document_id' => $idDocument]);
        if ( count($data) ) {
            return true;
        }
        return false;
    }

    /**
     * info update custom document
     *
     * @param idDocument | id of document
     * @author huynh
     */
    public function infoCustomDocument ($idDocument) {
        $sql = "SELECT T1.document_id, T1.document_name, T2.product_cd, M1.product_name, T1.document_contents,
                T1.disease, T1.medical_subjects_group_cd, T1.safety_information_flag, T1.off_label_information_flag,
                T1.start_date, T1.end_date, T1.document_type, T1.create_user_cd, T1.file_type,
                T3.source_document_id, T3.available_org_cd, T3.disuse_flag,
                (SELECT GROUP_CONCAT(T5.document_usage_id) FROM t_document_usage_situation T5 WHERE T5.document_id = T1.document_id) AS list_document_usage_id
                FROM t_document T1
                JOIN t_document_product T2 ON T2.document_id = T1.document_id
                JOIN t_customize_document_detail T3 ON T3.document_id = T1.document_id
                JOIN t_document_composition T4 ON T4.document_id = T1.document_id
                JOIN m_product M1 ON M1.product_cd = T2.product_cd
                ";
        if ( $idDocument ) {
            $sql = $sql . " WHERE T4.document_id = $idDocument";
        }
        return $this->_first($sql, []);
    }
    /**
     * get list product in table m_product on column A3
     * https://app.diagrams.net/#G1OIRA2X52cH-9rCV_d8u-ttXSgvYZH9EJ
     *
     * @author huynh
     */
    public function listProduct () {
        $sql = "SELECT product_name, product_cd FROM m_product";
        return $this->_find($sql, []);
    }
    /**
     * get list all in table m_medical_subjects_group on column A6
     * https://app.diagrams.net/#G1OIRA2X52cH-9rCV_d8u-ttXSgvYZH9EJ
     *
     * @author huynh
     */
    public function listMedicalSubjectGroup () {
        $sql = "SELECT medical_subjects_group_name, medical_subjects_group_cd FROM m_medical_subjects_group";
        return $this->_find($sql, []);
    }
    /**
     * get group definition value table m_variable_definition to save into t_document
     * https://app.diagrams.net/#G1OIRA2X52cH-9rCV_d8u-ttXSgvYZH9EJ
     *
     * @author huynh
     */
    public function listVariableDefinition () {
        $sql = "SELECT definition_value, definition_label FROM m_variable_definition WHERE definition_name = '有無選択肢'";
        return $this->_find($sql, []);
    }
    /**
     * get list document custom in table t_document_composition
     * https://app.diagrams.net/#G1OIRA2X52cH-9rCV_d8u-ttXSgvYZH9EJ
     *
     * @author huynh
     */
    public function listDocumentCustom ($idDocument) {
        // $urlAws = config('filesystems.disks.s3.url');
        $now = Carbon::now()->format('Y-m-d');
        $sql = "SELECT T1.*, T4.cover_flag, T7.document_name,
                (SELECT GROUP_CONCAT(T2.document_usage_id) FROM t_document_usage_situation T2 WHERE T2.document_id = T1.document_id) AS list_document_usage_id,
                T7.file_type,
                (
                    CASE T7.file_type
                    WHEN 10 THEN (CONCAT('document/original/doc/', T1.original_document_id, '/' ,T1.original_document_page_num, '/', 'normal.jpg'))
                    ELSE (CONCAT('document/original/video/', T1.original_document_id, '/', 'normal.jpg'))
                    END
                ) AS thumbnail,
                (
                    CASE T7.file_type
                    WHEN 10 THEN (CONCAT('document/original/doc/',T1.original_document_id,'/',T1.original_document_page_num, '/', T1.original_document_page_num, '.pdf'))
                    ELSE (CONCAT( 'document/original/video/',T1.original_document_id, '/' ,T1.original_document_id, '.mp4'))
                    END
                ) AS file_url,
                T7.start_date, T7.end_date,
                (
                    CASE WHEN (T7.start_date <= :datenow1 AND T7.end_date >= :datenow2)
                    THEN 1
                    ELSE 0
                    END
                ) AS time_lable_flag
                -- T3.file_url AS thumbnail
                FROM t_document_composition T1
                JOIN t_document T7 ON T7.document_id = T1.original_document_id
                JOIN t_original_document_file_page T2 ON T2.original_document_id = T1.original_document_id AND T2.original_document_page_num = T1.original_document_page_num
                -- JOIN t_file T3 ON T3.file_id = T2.file_id
                JOIN t_original_document_detail T6 ON T6.document_id = T1.original_document_id
                LEFT JOIN t_file T3 ON T3.file_id = T2.file_url
                JOIN m_document_category T4 ON T4.document_category_cd = T6.document_category_cd
                WHERE T1.document_id = $idDocument";
        return $this->_find($sql, [
            'datenow1' => $now,
            'datenow2' => $now,
            // 'url_aws_3' => $urlAws,
            // 'url_aws_4' => $urlAws,
        ]);
    }
    /**
     * insert or update custom document
     *
     * @param idDocument | id of document
     * @author huynh
     */
    public function saveCustomDocument ($request, $idDocument, $action) {
        // get info form
        $userCd = $this->getCurrentUser();
        $typeCreate = $request->get('type_create_copy');
        $documentCopy = $request->get('document_copy');
        $nameDocument = $request->get('document_name');
        $productCd = $request->get('product_cd');
        $disease = $request->get('disease');
        $medicalSubjectsGroupCd = $request->get('medical_subjects_group_cd');
        $safetyInformationFlag = $request->get('safety_information_flag');
        $offLabelInformationFlag = $request->get('off_label_information_flag');
        $documentContents = $request->get('document_contents');
        $documentCustomSlides = json_decode($request->get('document_custom_slides'), true);
        // excute data slider
        $dataSlider = $this->dataSlider ($documentCustomSlides, $documentCopy, $typeCreate, $idDocument, $action);
        $startDate = $dataSlider['start_date'];
        $endDate = $dataSlider['end_date'];
        $fileType = $dataSlider['file_type'];
        $availableOrgCd = $dataSlider['available_org_cd'];
        $isSameStructureDocumentCopy = $dataSlider['is_same_structure_document_copy']; // 0 : not same, 1 : same
        $isSameStructureDocumentUpdate = $dataSlider['is_same_structure_document_update']; // 0 : not same, 1 : same
        // save into table t_document
        Log::info('start save document custome into table t_document');
        $idDocument = $this->saveTableDocument(
            $nameDocument, $documentContents, $disease, $medicalSubjectsGroupCd, $safetyInformationFlag,
            $offLabelInformationFlag, $userCd, $startDate, $endDate, $fileType, $idDocument, $action
        );
        Log::info('start save document custome into table t_document_composition');
        $dataInsertComposition = $this->dataInserComposition ($documentCustomSlides, $idDocument, $userCd, $request);
        // save into t_document_composition
        $this->saveTableDocumentComposition ($dataInsertComposition, $idDocument);
        Log::info('start save document custome into table t_document_product');
        // save into table t_document_product : product_name, product_cd
        $this->saveTableDocumentProduct ($productCd, $userCd, $idDocument, $action);
        Log::info('start save document custome into table t_customize_document_detail');
        // save into t_customize_document_detail
        $this->saveTableDocumentCustomizeDetail ($availableOrgCd, $userCd, $idDocument, $action, $typeCreate, $documentCopy, $isSameStructureDocumentCopy);
        // update source document id if case update and document current change data slide
        $this->updateSourceDocument ($isSameStructureDocumentUpdate, $idDocument, $action);
        Log::info('end save data into document custom');
        return $idDocument;
    }
    /**
     * Delete one custom document
     *
     * @param idDocument | id of document
     * @author huynh
     *
     * @return integer
     */
    public function deleteCustomDocument ($idDocument) {
        $sql1 = "SET FOREIGN_KEY_CHECKS = 0";
        DB::select($sql1);
        $sql2 = "DELETE T1, T2, T3, T4
                FROM t_document T1
                INNER JOIN t_document_composition T2 ON T2.document_id = T1.document_id
                INNER JOIN t_document_product T3 ON T3.document_id = T1.document_id
                INNER JOIN t_customize_document_detail T4 ON T4.document_id = T1.document_id
                WHERE T1.document_id = '$idDocument'";
        return DB::select($sql2);
        $sql3 = "SET FOREIGN_KEY_CHECKS = 1";
        DB::select($sql3);
    }
    private function saveTableDocument (
        $nameDocument, $documentContents, $disease, $medicalSubjectsGroupCd, $safetyInformationFlag,
        $offLabelInformationFlag, $userCd, $startDate, $endDate, $fileType, $idDocument, $action
    ) {
        $data = [
            'document_type' => $this->getDocumentTypeByVariable(),
            'document_name' => $nameDocument,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'document_contents' => $documentContents,
            'disease' => $disease,
            'medical_subjects_group_cd' => $medicalSubjectsGroupCd,
            'safety_information_flag' => $safetyInformationFlag,
            'off_label_information_flag' => $offLabelInformationFlag,
            'create_user_cd' => $userCd,
            'last_update_datetime' => $this->currentJapanDateTime('Y-m-d H:i:s'),
            'file_type' => $fileType
        ];
        if ( $action == 'update' ) {
            // update
            $data['document_id'] = $idDocument;
            $sql = "UPDATE t_document SET
                    document_type = :document_type,
                    document_name = :document_name,
                    start_date = :start_date,
                    end_date = :end_date,
                    document_contents = :document_contents,
                    disease = :disease,
                    medical_subjects_group_cd = :medical_subjects_group_cd,
                    safety_information_flag = :safety_information_flag,
                    off_label_information_flag = :off_label_information_flag,
                    create_user_cd = :create_user_cd,
                    last_update_datetime = :last_update_datetime,
                    file_type = :file_type
                    WHERE document_id = :document_id";
            $this->_update($sql, $data);
            return $idDocument;
        } else {
            // create
            $sql = "INSERT INTO t_document
            (
                document_type,
                document_name,
                start_date,
                end_date,
                document_contents,
                disease,
                medical_subjects_group_cd,
                safety_information_flag,
                off_label_information_flag,
                create_user_cd,
                last_update_datetime,
                file_type
            ) VALUES
            (
                :document_type,
                :document_name,
                :start_date,
                :end_date,
                :document_contents,
                :disease,
                :medical_subjects_group_cd,
                :safety_information_flag,
                :off_label_information_flag,
                :create_user_cd,
                :last_update_datetime,
                :file_type
            );";
            $this->_create($sql, $data);
            return $this->_lastInserted('t_document', 'document_id')->document_id;
        }
    }
    private function saveTableDocumentProduct ($productCd, $userCd, $idDocument, $action) {
        $data = [
            'product_cd' => $productCd,
            'document_id' => $idDocument
        ];
        if ( $action == 'update' ) {
            // update
            $sql = "UPDATE t_document_product SET
                    product_cd = :product_cd
                    WHERE document_id = :document_id";
            $this->_update($sql, $data);
        } else {
            // create
            $sql = "INSERT INTO t_document_product
            (
                document_id,
                product_cd
            ) VALUES
            (
                :document_id,
                :product_cd
            );";
            $this->_create($sql, $data);
        }
    }
    private function saveTableDocumentCustomizeDetail ($availableOrgCd, $userCd, $idDocument, $action, $typeCreate, $documentCopy, $isSameStructureDocumentCopy) {
        $sourceDocumentId = $idDocument;
        // if case create by copy , check if same structer document has copy then update source document id
        if ( $action == 'create' && $typeCreate && $typeCreate == Document::TYPE_COPY_DOCUMENT && $isSameStructureDocumentCopy ) {
            $sourceDocumentId = $documentCopy;
        }
        $data = [
            'document_id' => $idDocument,
            'source_document_id' => $sourceDocumentId,
            'available_org_cd' => $availableOrgCd
        ];
        if ( $action == 'update' ) {
            // update
            $data['document_id'] = $idDocument;
            $sql = "UPDATE t_customize_document_detail SET
                    source_document_id = :source_document_id,
                    available_org_cd = :available_org_cd
                    WHERE document_id = :document_id";
            $this->_update($sql, $data);
        } else {
            $data['disuse_flag'] = 0;
            // create
            $sql = "INSERT INTO t_customize_document_detail
            (
                document_id,
                source_document_id,
                available_org_cd,
                disuse_flag
            ) VALUES
            (
                :document_id,
                :source_document_id,
                :available_org_cd,
                :disuse_flag
            );";
            $this->_create($sql, $data);
        }
    }
    private function saveTableDocumentComposition ($dataInsert, $idDocument) {
        // remove all old with document_id
        $this->deleteTableDocumentComposition($idDocument);
        // save new
        $sqlInsert = "INSERT INTO t_document_composition
            (
                document_id,
                page_num,
                original_document_id,
                original_document_page_num,
                cover_html
            ) VALUES
            $dataInsert;";
            $this->_create($sqlInsert, []);
    }
    private function dataSlider ($documentCustomSlides, $documentCopy, $typeCreate, $idDocument, $action) {
        // variables need return
        $startDate = '';
        $endDate = '';
        $fileType = 0;
        $availableOrgCd = 0;
        $isSameStructureDocumentCopy = 1; // 0 : not same, 1 : same
        $isSameStructureDocumentUpdate = 1; // 0 : not same, 1 : same
        // create array to caculator file type
        $fileTypeArray = [];
        $dataDocumentCopy = null;
        $dataDocumentUpdate = null;
        if ( $documentCopy && $typeCreate == Document::TYPE_COPY_DOCUMENT) {
            $sqlDocumentCopy = "SELECT document_id, page_num, original_document_id FROM t_document_composition WHERE document_id = $documentCopy ORDER BY page_num ASC";
            $dataDocumentCopy = DB::select($sqlDocumentCopy);
            if ( count($dataDocumentCopy) != count($documentCustomSlides) ) {
                $isSameStructureDocumentCopy = 0;
            }
        }
        if ( $action == 'update' ) {
            $sqlDocumentUpdate = "SELECT document_id, page_num, original_document_id FROM t_document_composition WHERE document_id = $idDocument ORDER BY page_num ASC";
            $dataDocumentUpdate = DB::select($sqlDocumentUpdate);
            if ( count($dataDocumentUpdate) != count($documentCustomSlides) ) {
                $isSameStructureDocumentUpdate = 0;
            }
        }
        // sort document slide by page num
        $pageNums = array();
        foreach ($documentCustomSlides as $key => $row)
        {
            $pageNums[$key] = $row['page_num'];
        }
        array_multisort($pageNums, SORT_ASC, $documentCustomSlides);
        foreach($documentCustomSlides as $key=>$documentCustom) {
            $content = $documentCustom['content'];
            $pageNum = $documentCustom['page_num'];
            $originalDocumentId = $documentCustom['original_document_id'];
            $originalDocumentPageNum = $documentCustom['original_document_page_num'];
            // caculator file startDate, endDate,fileType, availableOrgCd
            $sqlOriginal = "SELECT T1.start_date, T1.end_date, T1.file_type, (SELECT org_cd FROM m_org M1 WHERE M1.org_cd = T2.available_org_cd AND M1.layer_num = (SELECT MAX(M1.layer_num) FROM m_org) LIMIT 1) AS max_layer_num
                            FROM t_document T1
                            JOIN t_original_document_detail T2 ON T2.document_id = T1.document_id
                            WHERE T1.document_id = $originalDocumentId";
            $dataOriginalTemp = DB::select($sqlOriginal, []);

            if (count($dataOriginalTemp)) {
                if ( !$startDate || ($startDate && $dataOriginalTemp[0]->start_date > $startDate)) {
                    $startDate = $dataOriginalTemp[0]->start_date;
                }
                if ( !$endDate || ($endDate && $dataOriginalTemp[0]->end_date < $endDate)) {
                    $endDate = $dataOriginalTemp[0]->end_date;
                }
                $fileTypeArray[] = $dataOriginalTemp[0]->file_type;
                if ( !$availableOrgCd || ($availableOrgCd && (int)$dataOriginalTemp[0]->max_layer_num > $availableOrgCd) ) {
                    $availableOrgCd = $dataOriginalTemp[0]->max_layer_num;
                }
            }
            // compare 2 document case copy
            if ( $documentCopy && $typeCreate == Document::TYPE_COPY_DOCUMENT && $pageNum != 1 && isset($dataDocumentCopy[$key])) {
                $originalDocumentIdCopy = $dataDocumentCopy[$key]->original_document_id;
                if ($originalDocumentId != $originalDocumentIdCopy) {
                    $isSameStructureDocumentCopy = 0;
                }
            }
            // compare 2 document before and after update
            if ( $action == 'update' && isset($dataDocumentUpdate[$key])) {
                $originalDocumentIdUpdate = $dataDocumentUpdate[$key]->original_document_id;
                if ($originalDocumentId != $originalDocumentIdUpdate) {
                    $isSameStructureDocumentUpdate = 0;
                }
                // return 0;
                // exit(0);
            }
        }
        // caculator file type
        $fileTypeArray = array_unique($fileTypeArray);
        if ( count($fileTypeArray) == 1 ) {
            $fileType = $fileTypeArray[0];
        }
        if ( count($fileTypeArray) == 2 ) {
            $fileType = 30;
        }
        return [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'file_type' => $fileType,
            'available_org_cd' => $availableOrgCd,
            'is_same_structure_document_copy' => $isSameStructureDocumentCopy,
            'is_same_structure_document_update' => $isSameStructureDocumentUpdate
        ];
    }
    private function dataInserComposition ($documentCustomSlides, $idDocument, $userCd, $request) {

        $dataInsertComposition = "";
        foreach($documentCustomSlides as $key => $documentCustom) {
            $pageNum = $documentCustom['page_num'];
            $content = $this->updateContentHtml ($documentCustom['original_document_id'], $pageNum, $documentCustom['content'], $request);
            $originalDocumentId = $documentCustom['original_document_id'];
            $originalDocumentPageNum = $documentCustom['original_document_page_num'];
            $dataInsertComposition .= "(
                $idDocument,
                $pageNum,
                $originalDocumentId,
                $originalDocumentPageNum ,
                '$content'
            )";
            if ( $key < count($documentCustomSlides) - 1 ) {
                $dataInsertComposition .= ",";
            }
        }

        return $dataInsertComposition;
    }
    private function getDocumentTypeByVariable () {
        $sql = "SELECT * FROM m_variable_definition WHERE definition_label='カスタム資材'";
        $data = DB::select($sql, []);
        if ( count($data) ) {
            return $data[0]->definition_value;
        }
    }
    private function deleteTableDocumentComposition ($idDocument) {
        $sql1 = "SET FOREIGN_KEY_CHECKS = 0";
        DB::select($sql1);
        $sql2 = "DELETE FROM t_document_composition WHERE document_id = '$idDocument'";
        return DB::select($sql2);
        $sql3 = "SET FOREIGN_KEY_CHECKS = 1";
        DB::select($sql3);
    }
    private function updateSourceDocument ($isSameStructureDocumentUpdate, $idDocument, $action) {
        if ( $action == 'update' && $isSameStructureDocumentUpdate == 0 ) {
            // GET customize document detail to update
            $sqlGet = "SELECT GROUP_CONCAT(DISTINCT document_id) AS document_ids FROM t_customize_document_detail WHERE source_document_id = $idDocument";
            $dataCustomizeDetails = DB::select($sqlGet);
            if ( count($dataCustomizeDetails) ) {
                $documentIds = $dataCustomizeDetails[0]->document_ids;
                if ($documentIds) {
                    $sqlUpdate = "UPDATE t_customize_document_detail SET source_document_id = document_id WHERE document_id IN ($documentIds)";
                    $this->_update($sqlUpdate, []);
                }
            }
        }
    }
    /**
     * change status disue flag 0 <-> 1
     */
    public function updateStatusDisueFlag($status, $idDocument) {
        // update
        $data = [
            'disuse_flag' => $status,
            'document_id' => $idDocument
        ];
        $sql = "UPDATE t_customize_document_detail SET
        disuse_flag = :disuse_flag
        WHERE document_id = :document_id";
        $this->_update($sql, $data);
    }
    public function updateContentHtml ($idDocument, $pageNum, $content, $request) {
        $indexPage = $pageNum - 1;
        if ($request->hasFile("slide.".$indexPage)) {
            $newContent = json_decode(base64_decode($content), true);
            if ( $newContent ) {
                // upload file
                $fileName = 'normal.jpg';
                $filePath = "document/custom/doc/$idDocument/$pageNum";
                $request->file('slide')[$indexPage]->storePubliclyAs($filePath, $fileName, 'public');
                $fullPathFile = $this->encodeBase64String($filePath . '/' . $fileName);
                $newContent[0]['thumbnail'] = $fullPathFile;
                $content = base64_encode(json_encode($newContent));
            }
        }
        return $content;
    }
    /**
     * listDocumentId : 147, 153, 155, 164
     */
    public function getAllLayerOfUser ($listDocumentId) {
        $sql = "SELECT layer_num, layer_1, layer_2, layer_3, layer_4, layer_5
        FROM m_org
        WHERE org_cd IN (
            SELECT available_org_cd FROM t_original_document_detail WHERE document_id IN $listDocumentId AND available_org_cd IS NOT NULL
        )
        GROUP BY layer_1, layer_2, layer_3, layer_4, layer_5
        ORDER BY layer_num";
        $data = DB::select($sql, []);
        return $data;
    }
    /**
     * check documents input has same organization
     */
    public function isDocumentsSameOrg ($listDocumentId) {
        // define to : ('147','153','155','164')
        $listDocumentId = $this->_buildCommaString($listDocumentId, true);

        $groupLayerDocument = $this->getAllLayerOfUser($listDocumentId);
        $lengthLayderDocument = count($groupLayerDocument);
        if ($lengthLayderDocument) {
            $lastDataStandard = $groupLayerDocument[$lengthLayderDocument-1];
            $layer1Standard = $lastDataStandard->layer_1;
            $layer2Standard = $lastDataStandard->layer_2;
            $layer3Standard = $lastDataStandard->layer_3;
            $layer4Standard = $lastDataStandard->layer_4;
            $layer5Standard = $lastDataStandard->layer_5;
            foreach ($groupLayerDocument as $layers) {
                // check every layer
                $layer1 = $layers->layer_1;
                $layer2 = $layers->layer_2;
                $layer3 = $layers->layer_3;
                $layer4 = $layers->layer_4;
                $layer5 = $layers->layer_5;
                if ( $layer1 && $layer1Standard && $layer1Standard != $layer1 ) {
                    return false;
                    break;
                }
                if ( $layer2 && $layer2Standard && $layer2Standard != $layer2 ) {
                    return false;
                    break;
                }
                if ( $layer3 && $layer3Standard && $layer3Standard != $layer3 ) {
                    return false;
                    break;
                }
                if ( $layer4 && $layer4Standard && $layer4Standard != $layer4 ) {
                    return false;
                    break;
                }
                if ( $layer5 && $layer5Standard && $layer5Standard != $layer5 ) {
                    return false;
                    break;
                }
            }
        }
        return true;
    }
    private function getUserCreateDocumentAvailableOrgCdNull ($listDocumentId) {
        $sql = "SELECT created_by, available_org_cd FROM t_original_document_detail WHERE document_id IN $listDocumentId";
        $data = DB::select($sql, []);
        return $data;
    }
    /**
     * check user is can create list document
     */
    public function isUserCanBeCreateListDocument ($listDocumentId, $userCdLogin) {
        // define to : ('147','153','155','164')
        $listDocumentId = $this->_buildCommaString($listDocumentId, true);
        // check available org cd null and user have to user create or r40
        if ( !$this->isListDocumentValidNullAble($listDocumentId, $userCdLogin) ) {
            return false;
        }
        return true;
    }
    public function isListDocumentValidNullAble ($listDocumentId, $userCdLogin) {
        $dataNullAvailableOrgCd = $this->getUserCreateDocumentAvailableOrgCdNull($listDocumentId);
        if ( count($dataNullAvailableOrgCd) ) {
            // check is R40
            $r40 = config('role.DOC_MG.CODE');
            if ( !$this->isRole($r40, $userCdLogin) ) {
                if(!$dataNullAvailableOrgCd[0]->available_org_cd){
                    return true;
                }else{
                    $sql_org_user = "SELECT T3.org_cd
                                FROM m_user T1 
                                    JOIN m_user_org T2 ON T1.user_cd = T2.user_cd 
                                    JOIN m_org T3 ON T2.org_cd = T3.org_cd
                                WHERE T1.user_cd = :user_cd
                                GROUP BY layer_1";
                    $org_user = $this->_find($sql_org_user, ['user_cd' => $userCdLogin]) ?? [];
                    $check_org_available = false;
                    foreach($org_user as $org_cd){
                        if($dataNullAvailableOrgCd[0]->available_org_cd == $org_cd->org_cd){
                            $check_org_available = true;
                        }
                    }
                    if($check_org_available || $userCdLogin == $dataNullAvailableOrgCd[0]->created_by){
                        return true;
                    }else{
                        return false;
                    }
                }
            }
        }
        return true;
    }
    private function isRole($role, $userCdLogin) {
        $sql = "SELECT COUNT(*) AS count FROM m_user_role WHERE user_cd = :user_cd AND role = :role";
        $data = $this->_first($sql, [
            'user_cd' => $userCdLogin,
            'role' => $role
        ]);
        return $data->count > 0 ? true : false;
    }
    public function getListOriginalDocumentFromDocumentCustom ($idDocument) {
        $sql = "SELECT GROUP_CONCAT(DISTINCT original_document_id) AS original_document_id
        FROM t_document_composition WHERE document_id = :document_id";
        $data = $this->_first($sql, [
            'document_id' => $idDocument
        ]);
        $idDocumentOriginals = [];
        $originalDocumentId = $data->original_document_id;
        if ($originalDocumentId) {
            $idDocumentOriginals = explode(',', $originalDocumentId);
        }
        return $idDocumentOriginals;
    }
}
