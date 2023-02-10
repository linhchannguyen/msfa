DROP TABLE IF EXISTS `t_report_vacation`;

CREATE TABLE `t_report_vacation` (
   `report_id` bigint(20) unsigned NOT NULL COMMENT 'レポートID',
   `report_date` date NOT NULL COMMENT 'レポート日付',
   `user_cd` varchar(15) NOT NULL COMMENT 'ユーザコード',
   `created_by` varchar(15) NOT NULL COMMENT '登録者コード',
   `proxy_created_by` varchar(15) DEFAULT NULL COMMENT '登録者コード（代行ログイン者）',
   `created_at` datetime NOT NULL COMMENT '登録日時',
   `updated_by` varchar(15) NOT NULL COMMENT '更新者コード',
   `proxy_updated_by` varchar(15) DEFAULT NULL COMMENT '更新者コード（代行ログイン者コード）',
   `updated_at` datetime NOT NULL COMMENT '更新日時',
   constraint `t_report_vacation_PKC` PRIMARY KEY (`report_id`,`report_date`,`user_cd`)
 ) COMMENT='休暇レポート' ENGINE=InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;