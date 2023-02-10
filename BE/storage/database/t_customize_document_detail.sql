DROP TABLE IF EXISTS `t_customize_document_detail`;

CREATE TABLE `t_customize_document_detail` (
   `document_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '資材ID',
   `source_document_id` bigint(20) unsigned NOT NULL COMMENT 'コピー元資材ID',
   `available_org_cd` varchar(20) DEFAULT NULL COMMENT '組織コード(使用可能組織)',
   `disuse_flag` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '利用終了フラグ',
   `created_by` varchar(15) NOT NULL COMMENT '登録者コード',
   `proxy_created_by` varchar(15) DEFAULT NULL COMMENT '登録者コード（代行ログイン者）',
   `created_at` datetime NOT NULL COMMENT '登録日時',
   `updated_by` varchar(15) NOT NULL COMMENT '更新者コード',
   `proxy_updated_by` varchar(15) DEFAULT NULL COMMENT '更新者コード（代行ログイン者コード）',
   `updated_at` datetime NOT NULL COMMENT '更新日時',
   constraint `t_customize_document_detail_PKC` PRIMARY KEY (`document_id`)
 ) COMMENT='カスタム資材詳細' ENGINE=InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci ;
