<?php

namespace App\Repositories\Unapproved;

use App\Repositories\BaseRepository;
use App\Repositories\Unapproved\UnapprovedRepositoryInterface;

class UnapprovedRepository extends BaseRepository implements UnapprovedRepositoryInterface
{
    public function allData($request)
    {
        $sql = "SELECT T1.report_id,
                    T1.user_cd, 
                    T1.report_period_start_date,
                    T1.report_period_end_date,
                    T1.org_cd,
                    T4.org_label,
                    T1.submission_remarks,
                    T1.user_name,
                    T2.request_datetime
            FROM t_report T1 
            JOIN t_approval_request T2 on T1.report_id = T2.request_target_id AND T2.request_type = :request_type
            JOIN t_approval_request_detail T3 ON T2.request_id = T3.request_id
            LEFT JOIN m_org T4 on T1.org_cd = T4.org_cd
            WHERE T2.status_type = :status_type AND T1.report_status_type = :report_status_type
                AND (T2.request_user_cd = :request_user_cd OR T3.approval_user_cd = :approval_user_cd)
            GROUP BY T1.report_id
            ORDER BY T1.report_period_end_date DESC";
        return $this->_paginate(
            $sql,
            [
                'status_type' => 0,
                'report_status_type' => 20,
                'request_type' => $request->request_type,
                'request_user_cd' => $request->user_cd,
                'approval_user_cd' => $request->user_cd
            ],
            [
                "limit" => $request->limit,
                "offset" => $request->offset,
                "key" => "*",
                "key_type" => "normal"
            ]
        );
    }
}
