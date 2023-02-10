DROP TABLE IF EXISTS `t_select_person_group`;

CREATE TABLE `t_select_person_group` (
   `select_person_group_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'セレクト施設個人グループID',
   `user_cd` varchar(15) NOT NULL COMMENT 'ユーザコード',
   `select_person_group_name` varchar(50) NOT NULL COMMENT 'グループ名',
   `sort_order` tinyint(6) unsigned NOT NULL COMMENT '表示順',
   `created_by` varchar(15) NOT NULL COMMENT '登録者コード',
   `proxy_created_by` varchar(15) DEFAULT NULL COMMENT '登録者コード（代行ログイン者）',
   `created_at` datetime NOT NULL COMMENT '登録日時',
   `updated_by` varchar(15) NOT NULL COMMENT '更新者コード',
   `proxy_updated_by` varchar(15) DEFAULT NULL COMMENT '更新者コード（代行ログイン者コード）',
   `updated_at` datetime NOT NULL COMMENT '更新日時',
   constraint `t_select_person_group_PKC` PRIMARY KEY (`select_person_group_id`)
 ) COMMENT='セレクト施設個人グループ' ENGINE=InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci ;