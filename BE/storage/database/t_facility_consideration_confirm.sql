DROP TABLE IF EXISTS `t_facility_consideration_confirm`;

CREATE TABLE `t_facility_consideration_confirm` (
   `facility_consideration_id` bigint(20) unsigned NOT NULL COMMENT '注意事項ID',
   `user_cd` varchar(15) NOT NULL COMMENT 'ユーザコード',
   `confirmed_flag` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '確認済フラグ',
   `confirmed_datetime` datetime DEFAULT NULL COMMENT '確認日時',
   `created_by` varchar(15) NOT NULL COMMENT '登録者コード',
   `proxy_created_by` varchar(15) DEFAULT NULL COMMENT '登録者コード（代行ログイン者）',
   `created_at` datetime NOT NULL COMMENT '登録日時',
   `updated_by` varchar(15) NOT NULL COMMENT '更新者コード',
   `proxy_updated_by` varchar(15) DEFAULT NULL COMMENT '更新者コード（代行ログイン者コード）',
   `updated_at` datetime NOT NULL COMMENT '更新日時',
   constraint `t_facility_consideration_confirm_PKC` PRIMARY KEY (`facility_consideration_id`,`user_cd`)
 ) COMMENT='施設注意事項確認状況' ENGINE=InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci ;