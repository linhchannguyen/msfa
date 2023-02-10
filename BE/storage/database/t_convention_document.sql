DROP TABLE IF EXISTS `t_convention_document`;

CREATE TABLE `t_convention_document` (
  `convention_id` bigint(20) unsigned NOT NULL COMMENT '講演会ID',
  `document_id` bigint(20) unsigned NOT NULL COMMENT '資材ID',
  `created_by` varchar(15) NOT NULL COMMENT '登録者コード',
  `proxy_created_by` varchar(15) DEFAULT NULL COMMENT '登録者コード（代行ログイン者）',
  `created_at` datetime NOT NULL COMMENT '登録日時',
  `updated_by` varchar(15) NOT NULL COMMENT '更新者コード',
  `proxy_updated_by` varchar(15) DEFAULT NULL COMMENT '更新者コード（代行ログイン者コード）',
  `updated_at` datetime NOT NULL COMMENT '更新日時',
  constraint `t_convention_document_PKC` PRIMARY KEY (`convention_id`,`document_id`)
) COMMENT='講演会資材' ENGINE=InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci ;