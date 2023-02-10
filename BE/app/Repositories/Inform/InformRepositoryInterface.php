<?php

namespace App\Repositories\Inform;

use App\Repositories\BaseRepositoryInterface;

interface InformRepositoryInterface extends BaseRepositoryInterface
{
    //Search inform
    public function search($data, $limit, $offset);

    //Archive inform
    public function archived($data);

    //Unarchive inform
    public function unarchived($data);

    //Archive inform
    public function archiveAll($user_cd);

    //Read inform
    public function readInform($id);

    //Get installation information
    public function installed($user_cd);

    //Inform settings
    public function register($data);

    //Delete user exclusion inform ategory 
    public function delete($user_cd);

    //register infrom
    public function registerInform($user_cd = null, $user_approval, $content, $category = null);

    //register infrom parameter
    public function registerInformParameter($user_cd = null, $inform_id, $parameter_key, $parameter_value);
}
