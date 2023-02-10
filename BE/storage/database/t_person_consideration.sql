DROP TABLE IF EXISTS `t_person_consideration`;

CREATE TABLE `t_person_consideration` (
   `person_consideration_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '注意事項ID',
   `person_cd` varchar(15) NOT NULL COMMENT '個人コード',
   `consideration_type` varchar(3) NOT NULL COMMENT '注意事項種別コード',
   `consideration_contents` text DEFAULT NULL COMMENT '注意事項',
   `last_update_datetime` datetime DEFAULT NULL COMMENT '最終更新日時',
   `create_user_cd` varchar(15) DEFAULT NULL COMMENT 'ユーザコード(入力者)',
   `create_user_name` varchar(30) DEFAULT NULL COMMENT 'ユーザ名(入力者当時)',
   `create_org_cd` varchar(20) DEFAULT NULL COMMENT '組織コード(入力者当時)',
   `create_org_label` varchar(30) DEFAULT NULL COMMENT '組織ラベル(入力者当時)',
   `created_by` varchar(15) NOT NULL COMMENT '登録者コード',
   `proxy_created_by` varchar(15) DEFAULT NULL COMMENT '登録者コード（代行ログイン者）',
   `created_at` datetime NOT NULL COMMENT '登録日時',
   `updated_by` varchar(15) NOT NULL COMMENT '更新者コード',
   `proxy_updated_by` varchar(15) DEFAULT NULL COMMENT '更新者コード（代行ログイン者コード）',
   `updated_at` datetime NOT NULL COMMENT '更新日時',
   constraint `t_person_consideration_PKC` PRIMARY KEY (`person_consideration_id`)
 ) COMMENT='個人注意事項' ENGINE=InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci ;