DROP TABLE IF EXISTS `t_select_person_group_detail`;

CREATE TABLE `t_select_person_group_detail` (
   `select_person_group_id` bigint(20) unsigned NOT NULL COMMENT 'セレクト施設個人グループID',
   `facility_cd` varchar(25) NOT NULL COMMENT '施設コード',
   `person_cd` varchar(15) NOT NULL COMMENT '個人コード',
   `created_by` varchar(15) NOT NULL COMMENT '登録者コード',
   `proxy_created_by` varchar(15) DEFAULT NULL COMMENT '登録者コード（代行ログイン者）',
   `created_at` datetime NOT NULL COMMENT '登録日時',
   `updated_by` varchar(15) NOT NULL COMMENT '更新者コード',
   `proxy_updated_by` varchar(15) DEFAULT NULL COMMENT '更新者コード（代行ログイン者コード）',
   `updated_at` datetime NOT NULL COMMENT '更新日時',
   constraint `t_select_person_group_detail_PKC` PRIMARY KEY (`select_person_group_id`,`facility_cd`,`person_cd`)
 ) COMMENT='セレクト施設個人グループ明細' ENGINE=InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci ;