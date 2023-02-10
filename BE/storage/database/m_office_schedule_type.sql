DROP TABLE IF EXISTS `m_office_schedule_type`;

CREATE TABLE `m_office_schedule_type` (
   `office_schedule_type` varchar(3) NOT NULL COMMENT '社内予定区分',
   `office_schedule_type_name` varchar(20) NOT NULL COMMENT '社内予定区分名',
   `title_free_flag` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT 'タイトル自由記入フラグ',
   `sort_order` tinyint(6) unsigned NOT NULL COMMENT '表示順',
   `created_by` varchar(15) NOT NULL COMMENT '登録者コード',
   `proxy_created_by` varchar(15) DEFAULT NULL COMMENT '登録者コード（代行ログイン者）',
   `created_at` datetime NOT NULL COMMENT '登録日時',
   `updated_by` varchar(15) NOT NULL COMMENT '更新者コード',
   `proxy_updated_by` varchar(15) DEFAULT NULL COMMENT '更新者コード（代行ログイン者コード）',
   `updated_at` datetime NOT NULL COMMENT '更新日時',
   constraint `m_office_schedule_type_PKC` PRIMARY KEY (`office_schedule_type`)
 ) COMMENT='社内予定区分マスタ' ENGINE=InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci ;