DROP TABLE IF EXISTS `z_document_page_viewing_log`;

CREATE TABLE `z_document_page_viewing_log` (
   `document_page_viewing_log_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '視聴ページログID',
   `document_viewing_log_id` bigint(20) unsigned NOT NULL COMMENT '視聴ログID',
   `document_page_num` int(11) NOT NULL COMMENT '資材ページNo',
   `viewing_start_datetime` datetime NOT NULL COMMENT '視聴開始時間',
   `viewing_end_datetime` datetime NOT NULL COMMENT '視聴終了時間',
   `viewing_time` time NOT NULL COMMENT '視聴時間',
   `created_by` varchar(15) NOT NULL COMMENT '登録者コード',
   `proxy_created_by` varchar(15) DEFAULT NULL COMMENT '登録者コード（代行ログイン者）',
   `created_at` datetime NOT NULL COMMENT '登録日時',
   `updated_by` varchar(15) NOT NULL COMMENT '更新者コード',
   `proxy_updated_by` varchar(15) DEFAULT NULL COMMENT '更新者コード（代行ログイン者コード）',
   `updated_at` datetime NOT NULL COMMENT '更新日時',
   constraint `z_document_page_viewing_log_PKC` PRIMARY KEY (`document_page_viewing_log_id`)
 ) COMMENT='資材ページ視聴ログ' ENGINE=InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci ;