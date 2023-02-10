DROP TABLE IF EXISTS `t_original_document_detail`;

CREATE TABLE `t_original_document_detail` (
   `document_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '資材ID',
   `document_version` varchar(20) NOT NULL COMMENT 'バージョン',
   `document_category_cd` varchar(3) NOT NULL COMMENT '資材種別コード:学術資材、拡宣資材、広報資材',
   `file_id` bigint(20) unsigned NOT NULL COMMENT 'ファイルID',
   `management_org_cd` varchar(20) NOT NULL COMMENT '組織コード(管理部署当時)',
   `management_org_name` varchar(50) NOT NULL COMMENT '組織名(管理部署当時)',
   `available_org_cd` varchar(20) DEFAULT NULL COMMENT '組織コード(使用可能組織)',
   `parent_document_id` bigint(20) unsigned NOT NULL COMMENT '親資材ID',
   `created_by` varchar(15) NOT NULL COMMENT '登録者コード',
   `proxy_created_by` varchar(15) DEFAULT NULL COMMENT '登録者コード（代行ログイン者）',
   `created_at` datetime NOT NULL COMMENT '登録日時',
   `updated_by` varchar(15) NOT NULL COMMENT '更新者コード',
   `proxy_updated_by` varchar(15) DEFAULT NULL COMMENT '更新者コード（代行ログイン者コード）',
   `updated_at` datetime NOT NULL COMMENT '更新日時',
   constraint `t_original_document_detail_PKC` PRIMARY KEY (`document_id`)
 ) COMMENT='原本資材詳細' ENGINE=InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;