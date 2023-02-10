DROP TABLE IF EXISTS `t_convention_attendee_status_detail`;

CREATE TABLE `t_convention_attendee_status_detail` (
   `convention_attendee_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '講演会出席者ID',
   `status_item_cd` bigint(20) unsigned NOT NULL COMMENT '状況確認項目コード',
   `status_item_value` varchar(2) NOT NULL COMMENT '状況確認値',
   `update_date` date DEFAULT NULL COMMENT '更新日',
   `created_by` varchar(15) NOT NULL COMMENT '登録者コード',
   `proxy_created_by` varchar(15) DEFAULT NULL COMMENT '登録者コード（代行ログイン者）',
   `created_at` datetime NOT NULL COMMENT '登録日時',
   `updated_by` varchar(15) NOT NULL COMMENT '更新者コード',
   `proxy_updated_by` varchar(15) DEFAULT NULL COMMENT '更新者コード（代行ログイン者コード）',
   `updated_at` datetime NOT NULL COMMENT '更新日時',
   constraint `t_convention_attendee_status_detail_PKC` PRIMARY KEY (`convention_attendee_id`,`status_item_cd`)
 ) COMMENT='講演会出席者状況確認明細' ENGINE=InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci ;