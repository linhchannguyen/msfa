<?php

namespace App\Services;

use App\Enums\Document;
use App\Repositories\DocumentPowerPoint\DocumentPowerPointRepositoryInterface;
use App\Repositories\ManagerQuestionAndAwnser\ManagerQuestionAndAwnserInterface;

class DocumentPowerPointService {
    private $repo;
    private $repoQA;

    public function __construct(DocumentPowerPointRepositoryInterface $repo, ManagerQuestionAndAwnserInterface $repoQA)
    {
        $this->repo = $repo;
        $this->repoQA = $repoQA;
    }
    /**
     * get List document original
     * 
     * @author huynh
     */
    public function listOriginalDocument () {
        return $this->repo->getListOriginalDocument();
    }

    /**
     * get list item of a original document
     * 
     * @param idOriginal : id of document original
     * @author huynh
     */
    public function contentOriginalDocument ($idOriginal) {
        return $this->repo->getContentOriginalDocument($idOriginal);
    }
    /**
     * get info one custom document
     * 
     * @param idOriginal : id of document original
     * @author huynh
     */
    public function infoCustomDocument ($idDocument) {
        return $this->repo->infoCustomDocument($idDocument);
    }
    /**
     * get list product in table m_product on column A3
     * https://app.diagrams.net/#G1OIRA2X52cH-9rCV_d8u-ttXSgvYZH9EJ
     * 
     * @author huynh
     */
    public function products () {
        return $this->repo->listProduct();
    }
    /**
     * get list all in table m_medical_subjects_group on column A6
     * https://app.diagrams.net/#G1OIRA2X52cH-9rCV_d8u-ttXSgvYZH9EJ
     * 
     * @author huynh
     */
    public function medicalSubjectGroups () {
        return $this->repo->listMedicalSubjectGroup();
    }
    /**
     * get group definition value table m_variable_definition to save into t_document
     * https://app.diagrams.net/#G1OIRA2X52cH-9rCV_d8u-ttXSgvYZH9EJ
     * 
     * @author huynh
     */
    public function variableDefinitions () {
        return $this->repo->listVariableDefinition();
    }
    /**
     * get list document custom in table t_document_composition
     * https://app.diagrams.net/#G1OIRA2X52cH-9rCV_d8u-ttXSgvYZH9EJ
     * 
     * @author huynh
     */
    public function documentCustoms ($idDocument) {
        return $this->repo->listDocumentCustom ($idDocument);
    }
    /**
     * insert or update custom document
     * 
     * @param idDocument | id of document
     * @author huynh
     */
    public function saveCustomDocument ($request, $idDocument = '', $action = 'create') {
        return $this->repo->saveCustomDocument ($request, $idDocument, $action);
    }
    /**
     * Delete one custom document
     * 
     * @param idDocument | id of document
     * @author huynh
     */
    public function deleteCustomDocument ($idDocument) {
        return $this->repo->deleteCustomDocument ($idDocument);
    }
    public function updateStatusDisueFlag($status, $idDocument) {
        return $this->repo->updateStatusDisueFlag ($status, $idDocument);
    }
    public function listSlideShow ($idDocument) {
        return $this->repo->listSlideShow($idDocument);
    }
    public function infoDocument ($idQA) {
        return $this->repo->infoDocument($idQA);
    }
    public function givePointDocumentCopy ($userCdLogin, $idDocument) {
        $this->repoQA->givePointToUser ($userCdLogin, Document::TYPE_COPY_DOCUMENT_TEXT, $idDocument);
    }
}