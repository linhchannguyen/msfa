DROP TABLE IF EXISTS `t_detail_document`;

CREATE TABLE `t_detail_document` (
   `detail_id` bigint(20) unsigned NOT NULL COMMENT 'ディテールID',
   `document_id` bigint(20) unsigned NOT NULL COMMENT '資材ID',
   `created_by` varchar(15) NOT NULL COMMENT '登録者コード',
   `proxy_created_by` varchar(15) DEFAULT NULL COMMENT '登録者コード（代行ログイン者）',
   `created_at` datetime NOT NULL COMMENT '登録日時',
   `updated_by` varchar(15) NOT NULL COMMENT '更新者コード',
   `proxy_updated_by` varchar(15) DEFAULT NULL COMMENT '更新者コード（代行ログイン者コード）',
   `updated_at` datetime NOT NULL COMMENT '更新日時',
   constraint `t_detail_document_PKC` PRIMARY KEY (`detail_id`,`document_id`)
 ) COMMENT='ディテール資材' ENGINE=InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci ;