<?php

namespace App\Repositories\DocumentPowerPoint;

use App\Repositories\BaseRepositoryInterface;

interface DocumentPowerPointRepositoryInterface extends BaseRepositoryInterface
{
    public function getListOriginalDocument ();
    public function getContentOriginalDocument ($idOriginal);
    public function infoCustomDocument ($idDocument);
    public function listProduct ();
    public function listMedicalSubjectGroup ();
    public function listVariableDefinition ();
    public function listDocumentCustom ($idDocument);
    public function saveCustomDocument ($request, $idDocument, $action);
    public function deleteCustomDocument ($idDocument);
    public function updateStatusDisueFlag($status, $idDocument);
    public function listSlideShow ($idDocument);
    public function infoDocument ($idDocument);
    public function isDocumentsSameOrg ($listDocumentId);
    public function isUserCanBeCreateListDocument ($listDocumentId, $userCdLogin);
    public function getListOriginalDocumentFromDocumentCustom ($idDocument);
}
