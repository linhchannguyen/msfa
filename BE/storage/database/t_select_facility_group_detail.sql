DROP TABLE IF EXISTS `t_select_facility_group_detail`;

CREATE TABLE `t_select_facility_group_detail` (
   `select_facility_group_id` bigint(20) unsigned NOT NULL COMMENT 'セレクト施設グループID',
   `facility_cd` varchar(25) NOT NULL COMMENT '施設コード',
   `created_by` varchar(15) NOT NULL COMMENT '登録者コード',
   `proxy_created_by` varchar(15) DEFAULT NULL COMMENT '登録者コード（代行ログイン者）',
   `created_at` datetime NOT NULL COMMENT '登録日時',
   `updated_by` varchar(15) NOT NULL COMMENT '更新者コード',
   `proxy_updated_by` varchar(15) DEFAULT NULL COMMENT '更新者コード（代行ログイン者コード）',
   `updated_at` datetime NOT NULL COMMENT '更新日時',
   constraint `t_select_facility_group_detail_PKC` PRIMARY KEY (`select_facility_group_id`,`facility_cd`)
 ) COMMENT='セレクト施設グループ明細' ENGINE=InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci ;