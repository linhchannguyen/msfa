<?php

namespace App\Services;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\Filesystem\Factory;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use JWTAuth;

class UploadService {

    private $filesystem;

    public function __construct(Factory $filesystem) { 
        $this->filesystem = $filesystem;
    }

    protected function generateFileName(string $extension)
    {
        $uuid = str_replace('-', '', Str::uuid());
        return "$uuid.$extension";
    }

    public function uploadBlobServerBE(UploadedFile $file, $uploadDir) {
        $fileName = $file->getClientOriginalName();
        $filePath =$uploadDir . '/' . $fileName;
        Storage::disk('public')->put($filePath, file_get_contents($file));
        return [
            'name' => $fileName,
            'path' => $filePath,
            'url' => $filePath,
            'mime' => $file->getMimeType()
        ];
    }

    public function uploadBlobDocument(UploadedFile $file, $uploadDir,$content_document,$document_id) {
        $displayName = pathinfo($file->getClientOriginalName())['filename'];
        $type = $content_document === 'video' ? 'mp4' : 'pdf';
        // $fileName = $document_id.".".$type;
        $fileName = $displayName.".".$type;
        $filePath = $uploadDir . '/' . $fileName;
        $this->filesystem->disk('public')->put($filePath, file_get_contents($file));
        $url = $this->filesystem->disk('public')->url($filePath);
        return [
            'name' => $fileName,
            'path' => $filePath,
            'url' => $url,
            'mime' => $file->getMimeType()
        ];
    }
}