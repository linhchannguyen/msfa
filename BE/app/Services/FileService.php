<?php

namespace App\Services;

use App\Repositories\File\FileRepositoryInterface;
use App\Services\UploadService;
use App\Enums\FileUseType;
use App\Repositories\ConventionSearch\ConventionSearchRepositoryInterface;

class FileService
{
    private $repo;
    private $uploadService, $convention, $display_option_convention, $display_option_document;

    public function __construct(FileRepositoryInterface $repo, UploadService $uploadService, ConventionSearchRepositoryInterface $convention)
    {
        $this->repo = $repo;
        $this->convention = $convention;
        $this->uploadService = $uploadService;
        $this->display_option_convention = "講演会";
        $this->display_option_document = "資材";
    }

    public function saveFile($data)
    {
        $insertedData = [
            "use_type" => $data['use_type'],
            "file_name" => $data['path'],
            "display_name" => $data['name'],
            "mime_type" => $data['mime'],
            "file_url" => $data['url']
        ];

        $result = $this->repo->save($insertedData);

        if (!$result) {
            return false;
        }

        return $this->repo->lastInserted();
    }

    public function uploadAndSaveProfileImage($file)
    {
        $uploadedFile = $this->uploadService->uploadBlobServerBE($file, FileUseType::TYPE_PROFILE);
        $uploadedFile["use_type"] = FileUseType::TYPE_PROFILE;
        return $this->saveFile($uploadedFile);
    }

    public function uploadFileConvention($file)
    {
        $use_type = $this->convention->getUseType($this->display_option_convention);
        $uploadedFile = $this->uploadService->uploadBlobServerBE($file, FileUseType::TYPE_CONVENTION);
        $uploadedFile["use_type"] = $use_type->definition_value;
        return $this->saveFile($uploadedFile);
    }

    public function uploadFileDocument($file, $content_document, $document_id)
    {
        $use_type = $this->convention->getUseType($this->display_option_document);
        $uploadedFile = $this->uploadService->uploadBlobDocument($file, FileUseType::TYPE_DOCUMENT . '/original/' . $content_document . '/' . $document_id, $content_document, $document_id);
        $uploadedFile["use_type"] = $use_type->definition_value;
        return $this->saveFile($uploadedFile);
    }
}
