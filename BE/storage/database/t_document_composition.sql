DROP TABLE IF EXISTS `t_document_composition`;

CREATE TABLE `t_document_composition` (
   `document_id` bigint(20) unsigned NOT NULL COMMENT '資材ID',
   `page_num` int(11) NOT NULL COMMENT 'ページNo',
   `original_document_id` bigint(20) unsigned NOT NULL COMMENT '原本資材ID',
   `original_document_page_num` int(11) NOT NULL COMMENT '原本資材ファイルページNo',
   `cover_html` text DEFAULT NULL COMMENT '表紙HTML',
   `created_by` varchar(15) NOT NULL COMMENT '登録者コード',
   `proxy_created_by` varchar(15) DEFAULT NULL COMMENT '登録者コード（代行ログイン者）',
   `created_at` datetime NOT NULL COMMENT '登録日時',
   `updated_by` varchar(15) NOT NULL COMMENT '更新者コード',
   `proxy_updated_by` varchar(15) DEFAULT NULL COMMENT '更新者コード（代行ログイン者コード）',
   `updated_at` datetime NOT NULL COMMENT '更新日時',
   constraint `t_document_composition_PKC` PRIMARY KEY (`document_id`,`page_num`)
 ) COMMENT='資材構成' ENGINE=InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci ;