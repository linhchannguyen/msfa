<?php

namespace App\Services;

use App\Repositories\DocumentSearch\DocumentSearchRepositoryInterface;
use App\Repositories\Material\MaterialRepositoryInterface;
use App\Services\SystemParameterService;
use App\Services\FileService;
use App\Enums\FileUseType;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Log;

use AWS;
use App\Repositories\ConventionSearch\ConventionSearchRepositoryInterface;
use Imagick;
use App\Traits\Base64StringFileTrait;
class DocumentSearchService
{
    private $repo, $medical, $date, $serviceDate, $convention, $fileService, $display_option_document;
    use Base64StringFileTrait;
    public function __construct(
        DocumentSearchRepositoryInterface $repo,
        MaterialRepositoryInterface $medical,
        SystemParameterService $serviceDate,
        FileService $fileService,
        ConventionSearchRepositoryInterface $convention
    ) {
        $this->repo = $repo;
        $this->convention = $convention;
        $this->fileService = $fileService;
        $this->medical = $medical;
        $this->serviceDate = $serviceDate;
        $this->date = $this->serviceDate->getCurrentSystemDateTime();
        $this->display_option_document = "資材";
    }

    //List All Facility
    public function getData()
    {
        $variable = $this->repo->getVariable();
        $safety = [];
        foreach ($variable as $item) {
            $obj = array(
                "safety_value" => $item->definition_value,
                "safety_label" => $item->definition_label
            );
            array_push($safety, $obj);
        }
        $not_applicable = [];
        foreach ($variable as $item) {
            $obj = array(
                "not_applicable_value" => $item->definition_value,
                "not_applicable_label" => $item->definition_label
            );
            array_push($not_applicable, $obj);
        }
        $result['safety_information'] = $safety;
        $result['not_applicable_information'] = $not_applicable;
        $medical_subjects_group = $this->medical->allMedicalSubjects();
        $result['medical_subjects_group'] = $medical_subjects_group;
        return $result;
    }

    //List All Facility
    public function allData($request)
    {
        $day_new_icon = $this->repo->getDateNewIcon();
        $day_update_icon = $this->repo->getDateUpdateIcon();
        //check role user
        $user_role_R40 = $this->repo->getRoleUser($request->user_login);
        $request->role_user_r40 = isset($user_role_R40->user_cd);
        $result = $this->repo->allData($request);
        $date_new_icon = date("Y-m-d H:i:s", strtotime('-' . $day_new_icon->parameter_value . ' days', strtotime($this->date)));
        $date_update_icon = date("Y-m-d H:i:s", strtotime('-' . $day_update_icon->parameter_value . ' days', strtotime($this->date)));
        if (count($result['records']) > 0) {
            foreach ($result['records'] as &$item) {
                $item->new_icon = $item->created_at >= $date_new_icon ? 1 : 0;
                $item->update_icon = ($item->created_at != $item->updated_at && $item->updated_at >= $date_update_icon) ? 1 : 0; #
                $score_values = $this->repo->getScoreDocumentOriginalReviewComment($item->document_id, false);
                $item->comment = $score_values->sum_review;
                $item->point = $score_values->avg_review;
            }
        }
        return $result;
    }

    //List document Profile
    public function getDocumentDetail($request)
    {
        $document = $this->repo->getDetailDocument($request->document_id);
        if ($document->document_type == 10) {
            $document_title = $this->repo->getDetailDocumentOriginal($request->document_id);
            $score_values = $this->repo->getScoreDocumentOriginal($request->document_id);
            $score_values->count_situation = count($this->repo->getCountSituationDocumentOriginal($request->document_id));
        } elseif ($document->document_type == 20) {
            $document_title = $this->repo->getDetailDocumentCustom($request->document_id);
            $score_values = $this->repo->getScoreDocumentCustomReviewComment($request->document_id);
        }
        $result['document_title'] = $document_title;
        $result['score_values'] = $score_values;
        return $result;
    }

    //List document Profile
    public function getDocumentProfile($request)
    {
        $document = $this->repo->getDetailDocument($request->document_id);
        $profile = [];
        if ($document->document_type == 10) {
            $profile = $this->repo->getDocumentOriginalProfile($request->document_id);
        } elseif ($document->document_type == 20) {
            $profile = $this->repo->getDocumentCustomProfile($request->document_id);
        }
        $profile->document_org_user = 0;
        if ($profile->create_user_cd == $request->user_login || count($this->repo->checkOrgDocumentInOrgUser($profile->available_org_cd, $request->user_login)) > 0) {
            $profile->document_org_user = 1;
        }
        return $profile;
    }

    //get document custom
    public function getDocumentCustom($request)
    {
        $custom = $this->repo->getDocumentCustom($request);
        if (count($custom['records']) > 0) {
            foreach ($custom['records'] as &$item) {
                $item->list_composition = $this->repo->getListComposition($item->document_id);
                $item->document_org_user = 0;
                if (($item->create_user_cd == $request->user_login) || (count($this->repo->checkOrgDocumentInOrgUser($item->available_org_cd, $request->user_login)) > 0)) {
                    $item->document_org_user = 1;
                }
            }
        }
        return $custom;
    }

    //get Document Review Comment
    public function getDocumentReviewComment($request)
    {
        $document = $this->repo->getDetailDocument($request->document_id);
        if ($document->document_type == 10) {
            $score_values = $this->repo->getScoreDocumentOriginalReviewComment($request->document_id, $request->comprehensive_status);
            $distribution_graph = $this->repo->getDistributionGraphDocumentOriginalReviewComment($request->document_id, $request->comprehensive_status);
            $list_review_comment = $this->repo->getListReviewCommnetDocumentOriginalReviewComment($request);
        } elseif ($document->document_type == 20) {
            $score_values = $this->repo->getScoreDocumentCustomReviewComment($request->document_id);
            $distribution_graph = $this->repo->getDistributionGraphDocumentCustomReviewComment($request->document_id);
            $list_review_comment = $this->repo->getListReviewCommnetDocumentCustomReviewComment($request);
        }
        foreach($list_review_comment['records'] as $value){
            $value->file_url = $this->encodeBase64String($value->file_url);
        }
        $result['score_values'] = $score_values;
        $result['distribution_graph'] = $distribution_graph;
        $result['list_review_comment'] = $list_review_comment;
        return $result;
    }

    //get data index Document Registration()
    public function indexDocumentRegistration()
    {
        $result = $this->getData();
        $result['document_category'] = $this->repo->allDocumentCategory();
        return $result;
    }

    //get data index Document Registration()
    public function getDocumentRegistrationDetail($request)
    {
        $detail = $this->repo->getDocumentRegistrationDetail($request->document_id);
        if(isset($detail->document_id)){
            $detail->file_name = $this->encodeBase64String($detail->file_name);
        }
        return $detail;
    }

    //get data index Document Registration()
    public function saveDocumentRegistrationDetail($request)
    {
        if ($request->file_type == 10) {
            $request->content_document = 'doc';
        } elseif ($request->file_type == 20) {
            $request->content_document = 'video';
        }
        if ($request->document_id) {
            $insert_document = $this->repo->updateDocument($request);
            $this->repo->updateDocumentProduct($request);
            if (!$request->file_id) {
                $this->repo->deleteDocumentFilePage($request->document_id);
                $this->repo->deleteDocumentComposition($request->document_id);
                $files =  Storage::allFiles(FileUseType::TYPE_DOCUMENT . "/original/" . $request->content_document . "/" . $request->document_id);
                Storage::delete($files);
                Storage::disk('public')->deleteDirectory(FileUseType::TYPE_DOCUMENT . "/custom/doc/" . $request->document_id);
                $fileData = $this->fileService->uploadFileDocument($request->file('document_file'), $request->content_document, $request->document_id);
                $request->file_id = $fileData->file_id;
                if ($request->document_file->getClientOriginalExtension() !== 'mp4') {
                    $this->loadFileS3($request);
                } else {
                    $this->LoadVideo($request);
                }
            }
            $this->repo->updateOriginalDocumentDetail($request);
        } else {
            // add document
            $insert_document = $this->repo->insertDocument($request);
            $request->document_id = $insert_document->document_id;
            // add document product
            $this->repo->addDocumentProduct($request);
            $fileData = $this->fileService->uploadFileDocument($request->file('document_file'), $request->content_document, $request->document_id);
            $request->file_id = $fileData->file_id;
            $this->repo->addOriginalDocumentDetail($request);
            if ($request->document_file->getClientOriginalExtension() === 'pdf') {
                $this->loadFileS3($request);
            } else {
                $this->LoadVideo($request);
            }
        }
        return $insert_document;
    }

    //Load File S3
    public function loadFileS3($request)
    {
        set_time_limit(0);
        $folderUpload = FileUseType::TYPE_DOCUMENT . "/original/" . $request->content_document . "/" . $request->document_id;
        // url pdf original uploaded
        $displayName = pathinfo($request->file('document_file')->getClientOriginalName())['filename'];
        $urlPathPdfOriginal = $folderUpload . "/" . $displayName . ".pdf";
        // $files = Storage::allFiles($folderUpload);
        // $s3 = AWS::createClient('s3');
        // $s3->putObject(array(
        //     'Bucket'     => config('filesystems.disks.s3.bucket'),
        //     'Key'        => config('filesystems.disks.s3.key'),
        //     'visibility' => 'public',
        // ));
        // $s3->registerStreamWrapper();

        // huynh - 202220616 - update code for pdf version new START

        // upload file
        $fileName = randomStringUnique() . '.pdf';
        $filePath = "";
        $request->file('document_file')->storePubliclyAs($filePath, $fileName, 'public');
        // convert to version 1.4 of pdf
        $fileOld = Storage::disk('public')->path($fileName);
        $fileNameNew = randomStringUnique() . '.pdf';
        $fileNew = Storage::disk('public')->path($fileNameNew);
        shell_exec( "ghostscript -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dNOPAUSE -dQUIET -dBATCH -sOutputFile=".$fileNew." ".$fileOld."");

        // get count page pdf on version 1.4 pdf
        $fileNew = Storage::disk('public')->path($fileNameNew);

        // huynh - 202220616 - update code for pdf version new END

        $file = $request->file('document_file')->getPathName();
        $pdf = new \setasign\Fpdi\Fpdi();
        $pdf->AddPage();
        // set the source file
        $pageCount = $pdf->setSourceFile($fileNew); // update code for pdf version new
        // upload pdf to folder
        for ($i = 1; $i <= $pageCount; $i++) {
            $pdf = new \setasign\Fpdi\Fpdi();
            $pdf->AddPage();
            // set the source file
            $pageCount = $pdf->setSourceFile($fileNew);
            $tplId = $pdf->importPage($i);
            // use the imported page and place it at point 10,10 with a width of 100 mm
            $pdf->useTemplate($tplId, 0, 0, null, null, true);
            // random name file
            $nameFile = $i . ".pdf";
            $pdf->Output($nameFile, "F");
            $fullPathPdf = $folderUpload . "/" . $i . "/" . $nameFile;
            Storage::disk('public')->put($fullPathPdf,file_get_contents($nameFile));
            $name_file_img = "normal.jpg";
            $data = file_get_contents($nameFile);
            $imgExtension = new Imagick();
            $filePdf = Storage::disk('public')->path($urlPathPdfOriginal . "[".($i - 1)."]");
            $imgExtension->readImage($filePdf);
            // $imgExtension->readImageBlob($data);
            // $imgExtension->setIteratorIndex(0);
            $imgExtension->writeImages(Storage::disk('public')->path("normal.jpg"), true);
            $file_path = Storage::disk('public')->get("normal.jpg");
            $file_img = Storage::disk('public')->put($folderUpload . "/" . $i . "/" . $name_file_img, $file_path);
            // Storage::disk('s3')->setVisibility($folderUpload . "/" . $i . "/" . $nameFile, 'private');
            $document_file_page = [
                'original_document_id' => $request->document_id,
                'original_document_page_num' => $i,
                'mime_type' => 'docuemnt/img'
            ];
            $this->repo->addDocumentFilePage($document_file_page);
            $document_composition = [
                'document_id' => $request->document_id,
                'page_num' => $i,
                'original_document_id' => $request->document_id,
                'original_document_page_num' => $i,
                'cover_html' => null
            ];
            $this->repo->addDocumentComposition($document_composition);
        }
        // huynh - 202220616 - update code for pdf version new START
        // delete file pdf
        Storage::disk('public')->delete([$fileName, $fileNameNew]);
        // huynh - 202220616 - update code for pdf version new END
    }

    //Load File S3
    public function LoadVideo($request)
    {
        $folderUpload = FileUseType::TYPE_DOCUMENT . "/original/" . $request->content_document . "/" . $request->document_id;
        // $files = Storage::allFiles($folderUpload);
        // $s3 = AWS::createClient('s3');
        // $s3->putObject(array(
        //     'Bucket'     => config('filesystems.disks.s3.bucket'),
        //     'Key'        => config('filesystems.disks.s3.key'),
        //     'visibility' => 'public',
        // ));
        // $s3->registerStreamWrapper();

        $file = $request->file('document_file');
        $fileOutput = Storage::disk('public')->path("normal" . $request->document_id . ".jpg");
        $exec = "ffmpeg -i $file -vf 'select=eq(n\,0)' -q:v 3 $fileOutput";
        shell_exec($exec);
        $file_path = Storage::disk('public')->get("normal" . $request->document_id . ".jpg");
        $file_img = Storage::disk('public')->put($folderUpload . "/" .  "normal.jpg", $file_path);
        $document_file_page = [
            'original_document_id' => $request->document_id,
            'original_document_page_num' => 1,
            'mime_type' => 'docuemnt/img'
        ];
        $this->repo->addDocumentFilePage($document_file_page);
        $document_composition = [
            'document_id' => $request->document_id,
            'page_num' => 1,
            'original_document_id' => $request->document_id,
            'original_document_page_num' => 1,
            'cover_html' => null
        ];
        $this->repo->addDocumentComposition($document_composition);
    }

    //get data index Document Registration()
    public function getDocumentUsageSituation($request)
    {
        return $this->repo->getDocumentUsageSituation($request->document_id);
    }

    //get data index Document Registration()
    public function getDocumentComposition($request)
    {
        return $this->repo->getDocumentComposition($request->document_id);
    }

    //get data index Document Registration()
    public function deleteDocument($request)
    {
        $this->repo->deleteDocumentFilePage($request->document_id);
        $this->repo->deleteDocumentComposition($request->document_id);
        //delete Document Detail
        $this->repo->deleteDocumentDetail($request->document_id);
        //delete Document Product
        $this->repo->deleteDocumentProduct($request->document_id);
        //delete Document
        return $this->repo->deleteDocument($request->document_id);
    }
}
