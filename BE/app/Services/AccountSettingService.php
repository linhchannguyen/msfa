<?php

namespace App\Services;

use App\Repositories\AccountSetting\AccountSettingRepositoryInterface;
use App\Repositories\Organization\OrganizationRepositoryInterface;

use Illuminate\Console\Command;

class AccountSettingService
{
    private $repo,$org;

    public function __construct(AccountSettingRepositoryInterface $repo, OrganizationRepositoryInterface $org)
    {
        $this->repo = $repo;
        $this->org = $org;
    }

    public function getAccountInfo($userCd)
    {
        $account = $this->repo->getUserInfo($userCd);
        $org = $this->repo->findOrg($userCd);
        $result = [];
        if (count($org) > 0) {
            foreach ($org as $item) {
                $obj['definition'] = $item->definition;
                $obj['org_name'] = $item->org_label;
                $result[] = $obj;
            }
        }
        $account->user_cd = $userCd;
        $account->definition = $result;
        return $account;
    }

    public function getAccountPoint($userCd, $pointTargetType, $limit, $offset)
    {
        $data = $this->repo->getAccountPoint($userCd, $pointTargetType, $limit, $offset);
        $data['total_points'] = 0;
        if(count($data['records']) > 0){
            $data['total_points'] = $data['records'][0]->total_points;
        }
        return $data;
    }

    public function getPointTargetType()
    {
        return $this->repo->getPointTargetType();
    }

    public function findAccount($userCd)
    {
        $account = $this->repo->findAccount($userCd);
        return empty($account) ? false : $account;
    }

    public function changeAccountInfo($userCd, $data)
    {
        return $this->repo->changeAccountInfo($userCd, $data);
    }

    public function updateAvatar($userCd, $avatarId)
    {
        return $this->repo->updateAvatar($userCd, $avatarId);
    }
}
