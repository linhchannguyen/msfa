<?php

namespace App\Repositories\DocumentSearch;

use App\Repositories\BaseRepositoryInterface;

interface DocumentSearchRepositoryInterface extends BaseRepositoryInterface
{
    //get variable Type
    public function getVariable();
    //get all data Convention
    public function allData($request);
    //get days Latest period days
    public function getDateNewIcon();
    //get days Latest period days
    public function getDateUpdateIcon();
    //check role R40 by user
    public function getRoleUser($user_login);
    //get Document Detail
    public function getDetailDocument($document_id);
    //get Document Detail Original
    public function getDetailDocumentOriginal($document_id);
    //get Document Detail Custom
    public function getDetailDocumentCustom($document_id);
    //get Score Document Original
    public function getScoreDocumentOriginal($document_id);
    //get Score Document Original
    public function getCountSituationDocumentOriginal($document_id);
    //get Document Profile
    public function getDocumentOriginalProfile($document_id);
    //get Document Profile
    public function getDocumentCustomProfile($document_id);
    //get Document Custom
    public function getDocumentCustom($request);
    //get all Document Category
    public function allDocumentCategory();
    //get all Document Category
    public function getDocumentRegistrationDetail($document_id);
    //update Document
    public function updateDocument($request);
    //update Original Document Detail
    public function updateOriginalDocumentDetail($request);
    //update Document Product
    public function updateDocumentProduct($request);
    //add Document Product
    public function addDocumentProduct($request);
    //insert Document
    public function insertDocument($request);
    //insert Original Document Detail
    public function addOriginalDocumentDetail($request);
    //insert Document File Page
    public function addDocumentFilePage($data);
    //insert Document Composition
    public function addDocumentComposition($data);
    //delete Document
    public function deleteDocument($document_id);
    //delete Document Detail
    public function deleteDocumentDetail($document_id);
    //delete Document Product
    public function deleteDocumentProduct($document_id);
    //get Score Document Original Review Comment
    public function getScoreDocumentOriginalReviewComment($document_id, $comprehensive_status);
    //get Distribution Graph Document Original Review Comment
    public function getDistributionGraphDocumentOriginalReviewComment($document_id, $comprehensive_status);
    //get List Review Commnet Document Original Review Comment
    public function getListReviewCommnetDocumentOriginalReviewComment($request);
    //get Score Document Custom Review Comment
    public function getScoreDocumentCustomReviewComment($document_id);
    //get Distribution Graph Document Custom Review Comment
    public function getDistributionGraphDocumentCustomReviewComment($document_id);
    //get List Review Commnet Document Custom Review Comment
    public function getListReviewCommnetDocumentCustomReviewComment($request);
    //
    public function deleteDocumentFilePage($document_id);
    //
    public function deleteDocumentComposition($document_id);
    //
    public function getDocumentUsageSituation($document_id);
    //
    public function getDocumentComposition($document_id);
    //
    public function checkOrgDocumentInOrgUser($available_org_cd, $user_login);

    public function getListComposition($document_id);
}
