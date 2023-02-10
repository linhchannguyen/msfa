<?php

namespace App\Repositories\ManagementFile;

use App\Repositories\BaseRepository;
use App\Repositories\FacilityUser\FacilityUserRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Enums\FileviewS3;

class ManagementFileRepository extends BaseRepository implements ManagementFileInterface
{
    public function getUrlS3 ($type, $userCdLogin, $idConvention = null, $idDocument = null, $typeOrinal = null, $pageNum = null, $idFile = null) {
        $urlFile = ''; $sql = '';
        switch ($type) {
            case FileviewS3::AVATAR:
                $paramData = $userCdLogin;
                $sql = "SELECT T1.file_name AS url_file FROM t_file T1 JOIN t_user_profile T2 ON T2.profile_image_file_id = T1.file_id WHERE T2.user_cd = :param_data";
                break;
            case FileviewS3::DOCUMENT_ORIGINAL:
                $paramData = $idDocument;
                if ( $typeOrinal == FileviewS3::THUMBNAIL ) {
                    $sql = "SELECT (
                        CASE T2.file_type 
                        WHEN 10 THEN (CONCAT('document/original/doc/', T1.original_document_id, '/' ,T1.original_document_page_num, '/', 'normal.jpg'))
                        ELSE (CONCAT('document/original/video/', T1.original_document_id, '/', 'normal.jpg'))
                        END
                    ) AS url_file
                    FROM t_original_document_file_page T1 
                    JOIN t_document T2 ON T2.document_id = T1.original_document_id
                    WHERE T2.document_id = :param_data AND T1.original_document_page_num = :original_document_page_num AND T2.document_type = (SELECT definition_value FROM m_variable_definition WHERE definition_label='原本資材' LIMIT 1)";
                } else {
                    $sql = "SELECT (
                        CASE T2.file_type 
                        WHEN 10 THEN (CONCAT('document/original/doc/',T1.original_document_id,'/',T1.original_document_page_num, '/', T1.original_document_page_num, '.pdf'))
                        ELSE (CONCAT('document/original/video/',T1.original_document_id, '/' ,T1.original_document_id, '.mp4'))
                        END
                    ) AS url_file
                    FROM t_original_document_file_page T1 
                    JOIN t_document T2 ON T2.document_id = T1.original_document_id
                    WHERE T2.document_id = :param_data AND T1.original_document_page_num = :original_document_page_num AND T2.document_type = (SELECT definition_value FROM m_variable_definition WHERE definition_label='原本資材' LIMIT 1)";
                }
                break;
            case FileviewS3::DOCUMENT_CUSTOM:
                $paramData = $idDocument;
                $sql = "SELECT (
                     CONCAT('document/custom/doc/',T1.document_id,'/',T1.page_num, '/', 'normal.jpg')
                ) AS url_file
                FROM t_document_composition T1 
                JOIN t_document T2 ON T2.document_id = T1.document_id
                WHERE T2.document_id = :param_data AND T1.page_num = :original_document_page_num AND T2.document_type = (SELECT definition_value FROM m_variable_definition WHERE definition_label='カスタム資材' LIMIT 1)";
                break;
            case FileviewS3::CONVENTION:
                $paramData = $idConvention;
                $sql = "SELECT T1.file_name AS url_file FROM t_file T1 
                JOIN t_convention_file T2 ON T2.file_id = T1.file_id 
                WHERE T2.convention_id = :param_data AND T1.file_id = :file_id";
                break;
            default:
                $sql = '';
                break;
        }
        if ( $sql ) {
            $params = [
                'param_data' => $paramData
            ];
            if ($type == FileviewS3::DOCUMENT_ORIGINAL || $type == FileviewS3::DOCUMENT_CUSTOM) {
                $params['original_document_page_num'] = $pageNum;
            }
            if ( $type == FileviewS3::CONVENTION ) {
                $params['file_id'] = $idFile;
            }
            $data = DB::select($sql, $params);
            if ( count($data) ) {
                $urlFile = $data[0]->url_file;
            }
        }
        return $urlFile;
    }
    
    
}
