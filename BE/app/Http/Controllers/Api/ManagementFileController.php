<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Enums\FileviewS3;
use App\Services\ManagementFileService;
use Illuminate\Support\Facades\Storage;
use App\Services\RolePolicyService;

use function PHPUnit\Framework\isEmpty;

class ManagementFileController extends Controller
{
    private $service;
    private $role;
    public function __construct(ManagementFileService $service, RolePolicyService $role)
    {
        $this->service = $service;
        $this->role = $role;
    }
    /**
     * type
     * AVATAR : /files/avatar/idUser
     * DOCUMENT_ORIGINAL : /files/document-original/idDocument/type-original
     * DOCUMENT_CUSTOM : /files/document-custom/idDocument/page-num
     * CONVENTION : /files/convention/idConvention
     * id : idUser | idDocument | idConvention
     * data : type-original | page-num
     * type-original : thumbnail | file-data
     */
    public function getFile ($type, $id, Request $request) {
        $typeOrignal = $request->get('type-original');
        $pageNum = $request->get('page-num');
        $idFile = $request->get('file-id');
        
        $userCdLogin = $this->getCurrentUser();
        $roleList = $this->role->getRoleList($userCdLogin);
        // validate type
        if ( $type != FileviewS3::AVATAR && $type != FileviewS3::DOCUMENT_ORIGINAL && 
        $type != FileviewS3::DOCUMENT_CUSTOM && $type != FileviewS3::CONVENTION ) {
            return $this->responseErrorForbidden('forbiden');
        }
        // check is only role dev
        if ($type != FileviewS3::AVATAR && (count($roleList) == 0 || (count($roleList) == 1 && $roleList[0] == config('role.DEV.CODE'))) ) {
            return $this->responseErrorForbidden('forbiden');
        }
        if ( $type == FileviewS3::AVATAR ) {
            // if ($userCdLogin != $id) {
            //     return $this->responseErrorForbidden('forbiden'); 
            // }
            $urlFileS3 = $this->service->getFileUrlS3($type, $id, null, null, null, null);
        }
        if ( $type == FileviewS3::DOCUMENT_ORIGINAL ) {
            if ( empty($typeOrignal) || empty($pageNum) || ($typeOrignal != FileviewS3::THUMBNAIL && $typeOrignal != FileviewS3::FILE_DATA) ) {
                return $this->responseErrorForbidden('forbiden');
            }
            $urlFileS3 = $this->service->getFileUrlS3($type, $userCdLogin, null, $id, $typeOrignal, $pageNum);
        }
        if ( $type == FileviewS3::DOCUMENT_CUSTOM ) {
            if ( empty($pageNum) ) {
                return $this->responseErrorForbidden('forbiden');
            }
            $urlFileS3 = $this->service->getFileUrlS3($type, $userCdLogin, null, $id, null, $pageNum);
        }
        if ( $type == FileviewS3::CONVENTION ) {
            if (empty($idFile)) {
                return $this->responseErrorForbidden('forbiden');
            }
            $urlFileS3 = $this->service->getFileUrlS3($type, $userCdLogin, $id, null, null, null, $idFile);
        }
        if ( !$urlFileS3 ) {
            return $this->responseNotFound('not found');
        }
        return $this->stream($urlFileS3);
    }
    public function stream ($path) {
        // $path = 'document/original/video/151/151.mp4';
        $adapter = Storage::getAdapter();
        $client = $adapter->getClient();
        $client->registerStreamWrapper();
        $object = $client->headObject([
            'Bucket' => $adapter->getBucket(),
            'Key' => $path,
        ]);
        $fileName = basename($path);
        $fullUrlS3 = config('filesystems.disks.s3.url') .  '/' . $path;
        
        $headers = [
            'Last-Modified' => $object['LastModified'],
            'Accept-Ranges' => $object['AcceptRanges'],
            'Content-Length' => $object['ContentLength'],
            'Content-type'   => get_headers($fullUrlS3, 1)["Content-Type"], // image/jpeg
            'Content-Disposition' => 'attachment; filename='. $fileName,
        ];
        return response()->stream(function () use ($adapter, $path) {
            if ($stream = fopen("s3://{$adapter->getBucket()}/". $path, 'r')) {
                while (!feof($stream)) {
                    echo fread($stream, 1024);
                }
                fclose($stream);
            }    
        }, 200, $headers);
    }
}
