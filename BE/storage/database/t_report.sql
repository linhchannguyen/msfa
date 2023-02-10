DROP TABLE IF EXISTS `t_report`;

CREATE TABLE `t_report` (
   `report_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'レポートID',
   `report_period_start_date` date NOT NULL COMMENT 'レポート期間開始日付',
   `report_period_end_date` date NOT NULL COMMENT 'レポート期間終了日付',
   `report_status_type` varchar(3) NOT NULL COMMENT 'レポートステータス',
   `submission_remarks` text DEFAULT NULL COMMENT '提出者メモ',
   `user_cd` varchar(15) NOT NULL COMMENT 'ユーザコード(提出者)',
   `user_name` varchar(30) NOT NULL COMMENT 'ユーザ名(提出者当時)',
   `org_cd` varchar(20) NOT NULL COMMENT '組織コード(提出者)',
   `org_label` varchar(30) NOT NULL COMMENT '組織ラベル(提出者当時)',
   `created_by` varchar(15) NOT NULL COMMENT '登録者コード',
   `proxy_created_by` varchar(15) DEFAULT NULL COMMENT '登録者コード（代行ログイン者）',
   `created_at` datetime NOT NULL COMMENT '登録日時',
   `updated_by` varchar(15) NOT NULL COMMENT '更新者コード',
   `proxy_updated_by` varchar(15) DEFAULT NULL COMMENT '更新者コード（代行ログイン者コード）',
   `updated_at` datetime NOT NULL COMMENT '更新日時',
   constraint `t_report_PKC` PRIMARY KEY (`report_id`)
 ) COMMENT='レポート' ENGINE=InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci ;