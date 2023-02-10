<?php

namespace App\Services;

use App\Repositories\PersonGroup\PersonGroupRepositoryInterface;

class PersonGroupService
{
    private $repo;

    public function __construct(PersonGroupRepositoryInterface $repo)
    {
        $this->repo = $repo;
    }

    public function getData($request)
    {
        $person_group = $this->repo->allData($request);
        foreach ($person_group as $item) {
            $facility_type = $this->repo->listFacilityType($item->select_person_group_id);
            foreach ($facility_type as $element) {
                $element->children = $this->repo->allPerson($item->select_person_group_id, $element->facility_category_type);
            }
            $item->children  = $facility_type;
        }
        $result['person_group'] = $person_group;
        $result['sort_order'] = $person_group[count($person_group) - 1]->sort_order ?? "";
        return $result;
    }

    public function deletePersonGroup($select_person_group_id)
    {
        $this->repo->deleteGroupDetail($select_person_group_id);
        return $this->repo->deletePersonGroup($select_person_group_id);
    }

    public function updatePersonGroup($data)
    {
        $person_group_id = "";
        if (empty($data->select_person_group_id)) {
            $person_group_id = $this->repo->insertPersonGroup($data);
        } else {
            $person_group_id = $this->repo->updatePersonGroup($data);
            $this->repo->deleteGroupDetail($person_group_id);
            $this->repo->deletePersonGroupContent($person_group_id);
            $this->repo->deletePersonGroupDocument($person_group_id);
            $this->repo->deletePersonGroupProduct($person_group_id);
        }
        if (count($data->children) > 0) {
            foreach ($data->children as $item) {
                if ($item['content_cd']) {
                    $this->repo->updateGroupContent($data->user_cd, $person_group_id, $item['content_cd'], $item['facility_category_type']);
                }
                if ($item['product_cd']) {
                    $product_cd = explode(',', $item['product_cd']);
                    foreach ($product_cd as $product) {
                        $this->repo->updateGroupProduct($data->user_cd, $person_group_id, $product, $item['facility_category_type']);
                    }
                }
                if ($item['document_id']) {
                    $document_id = explode(',', $item['document_id']);
                    foreach ($document_id as $document) {
                        $this->repo->updateGroupDocument($data->user_cd, $person_group_id, $document, $item['facility_category_type']);
                    }
                }
                if (count($item['children']) > 0) {
                    foreach ($item['children'] as $facility) {
                        $this->repo->insertGroupDetail($data->user_cd, $person_group_id, $facility['facility_cd'], $facility['person_cd']);
                    }
                }
            }
        }
        return true;
    }

    public function changeSelectPersonGroup($data)
    {
        if (count($data->person) > 0) {
            foreach ($data->person as $item) {
                $data = (object)$item;
                $this->repo->updatePersonGroup($data);
            }
        }
        return true;
    }
}
