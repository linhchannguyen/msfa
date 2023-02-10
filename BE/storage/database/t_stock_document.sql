DROP TABLE IF EXISTS `t_stock_document`;

CREATE TABLE `t_stock_document` (
   `stock_id` bigint(20) unsigned NOT NULL COMMENT 'ストックID',
   `document_id` bigint(20) unsigned NOT NULL COMMENT '資材ID',
   `created_by` varchar(15) NOT NULL COMMENT '登録者コード',
   `proxy_created_by` varchar(15) DEFAULT NULL COMMENT '登録者コード（代行ログイン者）',
   `created_at` datetime NOT NULL COMMENT '登録日時',
   `updated_by` varchar(15) NOT NULL COMMENT '更新者コード',
   `proxy_updated_by` varchar(15) DEFAULT NULL COMMENT '更新者コード（代行ログイン者コード）',
   `updated_at` datetime NOT NULL COMMENT '更新日時',
   constraint `t_stock_document_PKC` PRIMARY KEY (`stock_id`,`document_id`)
 ) COMMENT='ストック資材' ENGINE=InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci ;