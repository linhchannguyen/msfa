<?php

namespace App\Http\Controllers\Api;

use App\Enums\Document;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\DocumentPowerPointService;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\Api\DocumentCustom\DocumentCustomRequest;
use App\Http\Requests\Api\DocumentCustom\DocumentCustomChangeDisueFlag;
use Illuminate\Support\Facades\DB;
use App\Services\SystemParameterService;
use Illuminate\Support\Facades\Storage;
use App\Traits\Base64StringFileTrait;
use Illuminate\Support\Facades\File;

class DocumentCustomController extends Controller
{
    use Base64StringFileTrait;
    protected $documentPowerpointService;
    protected $system;
    public function __construct(DocumentPowerPointService $documentPowerpointService, SystemParameterService $system) {
        $this->documentPowerpointService = $documentPowerpointService;
        $this->system = $system;
        $this->middleware('role.not:'.config('role.DEV.CODE'));
    }
    /**
     * get List document original
     *
     * @author huynh
     */
    public function listOriginalDocument () {
        try {
            $data = $this->documentPowerpointService->listOriginalDocument();
            return $this->responseSuccess('success', $data);
        } catch (\Throwable $th) {
            throw $th;
        }
    }
    /**
     * get list item of a original document
     *
     * @param idOriginal : id of document original
     * @author huynh
     */
    public function contentOriginalDocument ($idOriginal) {
        try {
            $data = $this->documentPowerpointService->contentOriginalDocument($idOriginal);
            // convert base64 url file
            foreach ($data as &$document) {
                $document->thumbnail = $this->encodeBase64String($document->thumbnail);
                $document->file_url = $this->encodeBase64String($document->file_url);
            }

            return $this->responseSuccess('success', $data);
        } catch (\Throwable $th) {
            throw $th;
        }
    }
    public function createCustomDocument (DocumentCustomRequest $request) {
        DB::beginTransaction();
        try {
            $customDocument = [];
            if ( $request->getMethod() == 'POST' ) {
                $idDocument = $this->documentPowerpointService->saveCustomDocument($request);
                // give point for user
                if ( $request->get('type_create_copy') == Document::TYPE_COPY_DOCUMENT ) {
                    $userCdLogin = $this->getCurrentUser();
                    $this->documentPowerpointService->givePointDocumentCopy ($userCdLogin, $idDocument);
                }
                $customDocument = $this->documentPowerpointService->infoCustomDocument($idDocument);
            }
            // list product to make selectbox
            $products = $this->documentPowerpointService->products();
            // list medical subject group to make selectbox
            $medicalSubjectGroups = $this->documentPowerpointService->medicalSubjectGroups();
            // list variable definition to make checkbox
            $variableDefinitions = $this->documentPowerpointService->variableDefinitions();
            DB::commit();

            return $this->responseSuccess(__('messages.save_successfully'), [
                'products' => $products,
                'medical_subject_groups' => $medicalSubjectGroups,
                'variable_definitions' => $variableDefinitions,
                'custom_document' => $customDocument
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
    public function updateCustomDocument (DocumentCustomRequest $request, $idDocument) {
        DB::beginTransaction();
        try {
            // data update
            $customDocument = $this->documentPowerpointService->infoCustomDocument($idDocument);
            if ( $request->getMethod() == 'POST' ) {
                // check document has usage
                if($customDocument->list_document_usage_id) {
                    return $this->responseSystemError(__('develop.document_custom_has_use'));
                }
                $this->documentPowerpointService->saveCustomDocument($request, $idDocument, 'update');
                // get again data after update
                $customDocument = $this->documentPowerpointService->infoCustomDocument($idDocument);
            }
            // list product to make selectbox
            $products = $this->documentPowerpointService->products();
            // list medical subject group to make selectbox
            $medicalSubjectGroups = $this->documentPowerpointService->medicalSubjectGroups();
            // list variable definition to make checkbox
            $variableDefinitions = $this->documentPowerpointService->variableDefinitions();
            // list document custom
            $customDocumentSlide = $this->documentPowerpointService->documentCustoms($idDocument);
            // check if thumbnail not exist then thumbnail value url document original and convert base64 url file
            foreach ($customDocumentSlide as $key => &$customDSlide) {
                $customDSlide->thumbnail = $this->encodeBase64String($customDSlide->thumbnail);
                $customDSlide->file_url = $this->encodeBase64String($customDSlide->file_url);
                $convertCoverJsonToArray = @json_decode(base64_decode($customDSlide->cover_html), true);
                if ($convertCoverJsonToArray && count($convertCoverJsonToArray) ) {
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
            DB::commit();
            return $this->responseSuccess(__('messages.update_successfully'), [
                'custom_document' => $customDocument,
                'list_custom_document_slide' => $customDocumentSlide,
                'products' => $products,
                'medical_subject_groups' => $medicalSubjectGroups,
                'variable_definitions' => $variableDefinitions,
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
    public function deleteCustomDocument (DocumentCustomRequest $request, $idDocument) {
        try {
            // list document custom
            $customDocumentSlide = $this->documentPowerpointService->documentCustoms($idDocument);
            // check document has usage
            if($customDocumentSlide[0]->list_document_usage_id) {
                return $this->responseSystemError(__('develop.document_custom_has_use'));
            }
            // convert base64 url file
            foreach ($customDocumentSlide as &$customDSlide) {
                $customDSlide->thumbnail = $this->encodeBase64String($customDSlide->thumbnail);
                $customDSlide->file_url = $this->encodeBase64String($customDSlide->file_url);
                $convertCoverJsonToArray = @json_decode(base64_decode($customDSlide->cover_html), true);
                if ($convertCoverJsonToArray && count($convertCoverJsonToArray) ) {
                    if ( isset($convertCoverJsonToArray[0]['thumbnail']) ) {
                        $thumbnailCustom = $convertCoverJsonToArray[0]['thumbnail'];
                        $urlPathThumbnailCustom = $this->decodeStringBase64(str_replace(config('filesystems.disks.public_server.url') . '/', '', $thumbnailCustom));
                        if (!File::exists($urlPathThumbnailCustom)) {
                            $convertCoverJsonToArray[0]['thumbnail'] = $this->encodeBase64String($customDSlide->thumbnail);
                            $customDSlide->cover_html = base64_encode(json_encode($convertCoverJsonToArray));
                        }
                    }
                }
            }

            $this->documentPowerpointService->deleteCustomDocument($idDocument);
            return $this->responseSuccess(__('messages.delete_successfully'), []);
        } catch (\Throwable $th) {
            throw $th;
        }
    }
    /**
     * change status disue flag 0 <-> 1
     *
     * @author : huynh
     */
    public function changeStatusDisueFlag (DocumentCustomChangeDisueFlag $request, $idDocument) {
        try {
            $status = $request->get('status');
            // update status
            $this->documentPowerpointService->updateStatusDisueFlag($status, $idDocument);
            // data update
            $customDocument = $this->documentPowerpointService->infoCustomDocument($idDocument);
            return $this->responseSuccess(__('messages.update_successfully'), $customDocument);
        } catch (\Throwable $th) {
            throw $th;
        }
    }
    public function slideShow (DocumentCustomRequest $request, $idDocument) {
        $infoDocument = $this->documentPowerpointService->infoDocument($idDocument);
        $data = $this->documentPowerpointService->listSlideShow($idDocument);
        return $this->responseSuccess('success', [
            'info_document' => $infoDocument,
            'list_document' => $data
        ]);
    }
}
