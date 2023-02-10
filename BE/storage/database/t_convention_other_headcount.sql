DROP TABLE IF EXISTS `t_convention_other_headcount`;

CREATE TABLE `t_convention_other_headcount` (
   `convention_id` bigint(20) unsigned NOT NULL COMMENT '講演会ID',
   `occupation_type` varchar(10) NOT NULL COMMENT '職種区分',
   `status_item_cd` varchar(10) NOT NULL COMMENT '状況確認項目コード',
   `headcount` int(4) DEFAULT NULL COMMENT '人数',
   `created_by` varchar(15) NOT NULL COMMENT '登録者コード',
   `proxy_created_by` varchar(15) DEFAULT NULL COMMENT '登録者コード（代行ログイン者）',
   `created_at` datetime NOT NULL COMMENT '登録日時',
   `updated_by` varchar(15) NOT NULL COMMENT '更新者コード',
   `proxy_updated_by` varchar(15) DEFAULT NULL COMMENT '更新者コード（代行ログイン者コード）',
   `updated_at` datetime NOT NULL COMMENT '更新日時',
   constraint `t_convention_other_headcount_PKC` PRIMARY KEY (`convention_id`,`occupation_type`,`status_item_cd`)
 ) COMMENT='講演会その他出席者数' ENGINE=InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci ;