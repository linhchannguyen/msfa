<?php

namespace App\Repositories\TargetSelectionPeriodManagement;

use App\Repositories\BaseRepository;
use App\Repositories\TargetSelectionPeriodManagement\TargetSelectionPeriodManagementRepositoryInterface;

class TargetSelectionPeriodManagementRepository extends BaseRepository implements TargetSelectionPeriodManagementRepositoryInterface
{
    protected $useAutoMeta = true;

    public function getTargetSelectionPeriodManagement($date_system)
    {
        $sql = "SELECT target_product_name,target_product_cd,selection_start_date,selection_end_date,start_date,end_date FROM m_target_product WHERE end_date >= :date_system ORDER BY sort_order ASC;";

        return $this->_find($sql,['date_system' => $date_system]);
    }

    public function saveTargetProduct($target_product_cd,$selection_start_date,$selection_end_date)
    {
        $sql = "
            UPDATE m_target_product
            SET selection_start_date = :selection_start_date,
                selection_end_date = :selection_end_date
            WHERE target_product_cd = :target_product_cd;";

        return $this->_update($sql,[
            'selection_start_date' => $selection_start_date,
            'selection_end_date' => $selection_end_date,
            'target_product_cd' => $target_product_cd
        ]);
    }
}
