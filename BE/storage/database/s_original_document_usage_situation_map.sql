DROP TABLE IF EXISTS `s_original_document_usage_situation_map`;

CREATE TABLE `s_original_document_usage_situation_map` (
   `original_document_id` bigint(20) unsigned NOT NULL COMMENT '原本資材ID',
   `usage_situation_id` bigint(20) unsigned NOT NULL COMMENT '使用状況ID',
   `created_by` varchar(15) NOT NULL COMMENT '登録者コード',
   `proxy_created_by` varchar(15) DEFAULT NULL COMMENT '登録者コード（代行ログイン者）',
   `created_at` datetime NOT NULL COMMENT '登録日時',
   `updated_by` varchar(15) NOT NULL COMMENT '更新者コード',
   `proxy_updated_by` varchar(15) DEFAULT NULL COMMENT '更新者コード（代行ログイン者コード）',
   `updated_at` datetime NOT NULL COMMENT '更新日時',
   constraint `s_original_document_usage_situation_map_PKC` PRIMARY KEY (`original_document_id`,`usage_situation_id`)
 ) COMMENT='原本資材評価マップ' ENGINE=InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci ;