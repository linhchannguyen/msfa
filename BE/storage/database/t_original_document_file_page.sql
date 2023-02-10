DROP TABLE IF EXISTS `t_original_document_file_page`;

CREATE TABLE `t_original_document_file_page` (
   `original_document_id` bigint(20) unsigned NOT NULL COMMENT '原本資材ID',
   `original_document_page_num` int(11) NOT NULL COMMENT 'ページNo',
   `file_name` varchar(100) DEFAULT NULL COMMENT 'ファイル名',
   `mime_type` varchar(128) DEFAULT NULL COMMENT 'コンテンツタイプ',
   `file_url` text DEFAULT NULL COMMENT 'ファイルURL',
   `created_by` varchar(15) NOT NULL COMMENT '登録者コード',
   `proxy_created_by` varchar(15) DEFAULT NULL COMMENT '登録者コード（代行ログイン者）',
   `created_at` datetime NOT NULL COMMENT '登録日時',
   `updated_by` varchar(15) NOT NULL COMMENT '更新者コード',
   `proxy_updated_by` varchar(15) DEFAULT NULL COMMENT '更新者コード（代行ログイン者コード）',
   `updated_at` datetime NOT NULL COMMENT '更新日時',
   constraint `t_original_document_file_page_PKC` PRIMARY KEY (`original_document_id`,`original_document_page_num`)
 ) COMMENT='原本資材ファイルページ' ENGINE=InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci ;