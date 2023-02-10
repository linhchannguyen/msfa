DROP TABLE IF EXISTS `t_document_usage_situation`;

CREATE TABLE `t_document_usage_situation` (
   `usage_situation_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '使用状況ID',
   `document_id` bigint(20) unsigned NOT NULL COMMENT '資材ID',
   `document_usage_type` varchar(3) DEFAULT NULL COMMENT '資材使用機能区分',
   `document_usage_id` bigint(20) unsigned DEFAULT NULL COMMENT '資材使用機能ID',
   `usage_org_label` varchar(50) NOT NULL COMMENT '組織ラベル(使用者当時)',
   `usage_user_cd` varchar(15) NOT NULL COMMENT 'ユーザコード（使用者当時）',
   `usage_user_name` varchar(50) NOT NULL COMMENT 'ユーザ名（使用者当時）',
   `usage_datetime` datetime NOT NULL COMMENT '使用日時',
   `created_by` varchar(15) NOT NULL COMMENT '登録者コード',
   `proxy_created_by` varchar(15) DEFAULT NULL COMMENT '登録者コード（代行ログイン者）',
   `created_at` datetime NOT NULL COMMENT '登録日時',
   `updated_by` varchar(15) NOT NULL COMMENT '更新者コード',
   `proxy_updated_by` varchar(15) DEFAULT NULL COMMENT '更新者コード（代行ログイン者コード）',
   `updated_at` datetime NOT NULL COMMENT '更新日時',
   constraint `t_document_usage_situation_PKC` PRIMARY KEY (`usage_situation_id`)
 ) COMMENT='資材使用状況' ENGINE=InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci ;