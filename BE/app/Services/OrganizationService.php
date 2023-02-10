<?php

namespace App\Services;

use App\Repositories\Organization\OrganizationRepositoryInterface;

class OrganizationService
{
    private $repo;

    public function __construct(OrganizationRepositoryInterface $repo)
    {
        $this->repo = $repo;
    }

    public function getListData()
    {
        $result = [];
        $org_obj = [];
        $title = '組織階層名';
        $title_org = $this->repo->allTitleOrg($title);
        $layer_all = $this->repo->allOrganizationLayer1(1);
        if (count($layer_all) > 0) {
            foreach ($layer_all as $item_layer_1) {
                $obj_layer_2 = [];
                $layer_2 = $this->repo->allOrganizationLayer2(2, $item_layer_1->org_cd);
                if (count($layer_2) > 0) {
                    foreach ($layer_2 as $item_layer_2) {
                        $obj_layer_3 = [];
                        $layer_3 = $this->repo->allOrganizationLayer3(3, $item_layer_1->org_cd, $item_layer_2->org_cd);
                        if (count($layer_3) > 0) {
                            foreach ($layer_3 as $item_layer_3) {
                                $layer_4 = $this->repo->allOrganizationLayer4(4, $item_layer_1->org_cd, $item_layer_2->org_cd, $item_layer_3->org_cd);
                                $obj_layer_4 = [];
                                if (count($layer_3) > 0) {
                                    foreach ($layer_4 as $item_layer_4) {
                                        $layer_5 = $this->repo->allOrganizationLayer5(5, $item_layer_1->org_cd, $item_layer_2->org_cd, $item_layer_3->org_cd, $item_layer_4->org_cd);
                                        $item_layer_4->children = $layer_5 ?? [];
                                        $obj_layer_4[] = $item_layer_4;
                                    }
                                }
                                $item_layer_3->children = $obj_layer_4;
                                $obj_layer_3[] = $item_layer_3;
                            }
                        }
                        $item_layer_2->children = $obj_layer_3;
                        $obj_layer_2[] = $item_layer_2;
                    }
                }
                $item_layer_1->children = $obj_layer_2;
                $org_obj[] = $item_layer_1;
            }
        }
        $result['title'] = $title_org;
        $result['org_obj'] = $org_obj;
        return $result;
    }

    public function getListUser($data)
    {
        return $this->repo->allUser($data);
    }

    protected function _buildOrganizationTree($data, $tmpData = [])
    {
        foreach ($data as $record) {

            // If the record is root
            if ($record->layer_num == 1) {
                $tmpData[$record->org_cd] = $record;
                continue;
            }

            // If the record is a child
            $parentCdColumn = 'layer_' . ($record->layer_num - 1);
            $parentCd = $record->$parentCdColumn;

            // If the parent is exist in $tmpData
            if (isset($tmpData[$parentCd]) && is_object($tmpData[$parentCd])) {
                if (!isset($tmpData[$parentCd]->children)) {
                    $tmpData[$parentCd]->children = [];
                }

                array_push($tmpData[$parentCd]->children, $record);
            } else {
                $tmpData[$parentCd] = $this->_buildOrganizationTree([$record], $tmpData[$parentCd]->children);
            }
        }

        return $tmpData;
    }

    public function getMainOrganizationsByUser($user_cd)
    {
        return $this->repo->getMainOrganizationsByUser($user_cd);
    }

    public function query(&$orgs, $layer_num, $org_cd)
    {
        $return_orgs = [];
        array_walk($orgs, function (&$item, $key) use (&$return_orgs, $layer_num, &$orgs, $org_cd) {
            if (($item->layer_num ?? "") == $layer_num && $item->org_cd == $org_cd) {
                $return_orgs = $item;
                unset($orgs[$key]);
            }
        });
        return $return_orgs;
    }
}
