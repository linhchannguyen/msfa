DROP TABLE IF EXISTS `m_facility_person_segment_type`;

CREATE TABLE `m_facility_person_segment_type` (
   `segment_type` varchar(3) NOT NULL COMMENT 'セグメント区分',
   `segment_name` varchar(5) NOT NULL COMMENT 'セグメント名',
   `uneditable_flag` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '編集不可フラグ',
   `start_date` date NOT NULL COMMENT '適用期間開始日',
   `end_date` date NOT NULL COMMENT '適用期間終了日',
   `sort_order` tinyint(6) unsigned NOT NULL COMMENT '表示順',
   `created_by` varchar(15) NOT NULL COMMENT '登録者コード',
   `proxy_created_by` varchar(15) DEFAULT NULL COMMENT '登録者コード（代行ログイン者）',
   `created_at` datetime NOT NULL COMMENT '登録日時',
   `updated_by` varchar(15) NOT NULL COMMENT '更新者コード',
   `proxy_updated_by` varchar(15) DEFAULT NULL COMMENT '更新者コード（代行ログイン者コード）',
   `updated_at` datetime NOT NULL COMMENT '更新日時',
   constraint `m_facility_person_segment_type_PKC` PRIMARY KEY (`segment_type`)
 ) COMMENT='施設個人セグメントマスタ' ENGINE=InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci ;