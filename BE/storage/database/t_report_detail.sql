DROP TABLE IF EXISTS `t_report_detail`;

CREATE TABLE `t_report_detail` (
   `report_id` bigint(20) unsigned NOT NULL COMMENT 'レポートID',
   `report_detail_type` varchar(3) NOT NULL COMMENT 'レポート明細機能区分',
   `report_detail_id` bigint(20) unsigned NOT NULL COMMENT 'レポート明細機能ID',
   `report_detail_note` text DEFAULT NULL COMMENT 'レポート明細特記',
   `created_by` varchar(15) NOT NULL COMMENT '登録者コード',
   `proxy_created_by` varchar(15) DEFAULT NULL COMMENT '登録者コード（代行ログイン者）',
   `created_at` datetime NOT NULL COMMENT '登録日時',
   `updated_by` varchar(15) NOT NULL COMMENT '更新者コード',
   `proxy_updated_by` varchar(15) DEFAULT NULL COMMENT '更新者コード（代行ログイン者コード）',
   `updated_at` datetime NOT NULL COMMENT '更新日時',
   constraint `t_report_detail_PKC` PRIMARY KEY (`report_id`,`report_detail_type`,`report_detail_id`)
 ) COMMENT='レポート明細' ENGINE=InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci ;