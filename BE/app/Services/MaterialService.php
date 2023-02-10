<?php

namespace App\Services;

use App\Repositories\Material\MaterialRepositoryInterface;
use App\Repositories\DocumentSearch\DocumentSearchRepositoryInterface;

class MaterialService
{
    private $repo, $document;

    public function __construct(DocumentSearchRepositoryInterface $document, MaterialRepositoryInterface $repo)
    {
        $this->repo = $repo;
        $this->document = $document;
    }

    public function getData($data)
    {
        $result['material'] = $this->repo->allMaterial();
        $result['medical_subjects'] = $this->repo->allMedicalSubjects();
        $safety = $this->repo->allSafetyInformation();
        array_unshift($safety, array("safety_information_flag" => "", "safety_information_label" => "全て"));
        $result['safety'] = $safety;
        $off_label = $this->repo->allOffLabelInformation();
        array_unshift($off_label, array("off_label_information_flag" => "", "off_label_information_label" => "全て"));
        $result['off_label'] = $off_label;
        return $result;
    }

    public function getDataFilter($data)
    {
        $document = $this->repo->allDataFilter($data);
        foreach ($document as $item) {
            if ($item->document_type == 10) {
                $score_values = $this->document->getScoreDocumentOriginal($item->document_id);
            } elseif ($item->document_type == 20) {
                $score_values = $this->document->getScoreDocumentCustomReviewComment($item->document_id);
            }
            $item->comment = $score_values->sum_review ?? 0;
            $item->point = $score_values->avg_review ?? 0;
        }
        return $document;
    }

    public function getOrgDocuemnt($data)
    {
        return $this->repo->getOrgDocuemnt($data->org_cd);
    }
}
