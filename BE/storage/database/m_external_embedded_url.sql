DROP TABLE IF EXISTS `m_external_embedded_url`;

CREATE TABLE `m_external_embedded_url` (
   `external_embedded_url_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '外部埋め込みURLID',
   `external_url` varchar(200) NOT NULL COMMENT '外部URL',
   `created_by` varchar(15) NOT NULL COMMENT '登録者コード',
   `proxy_created_by` varchar(15) DEFAULT NULL COMMENT '登録者コード（代行ログイン者）',
   `created_at` datetime NOT NULL COMMENT '登録日時',
   `updated_by` varchar(15) NOT NULL COMMENT '更新者コード',
   `proxy_updated_by` varchar(15) DEFAULT NULL COMMENT '更新者コード（代行ログイン者コード）',
   `updated_at` datetime NOT NULL COMMENT '更新日時',
   constraint `m_external_embedded_url_PKC` PRIMARY KEY (`external_embedded_url_id`)
 ) COMMENT='外部埋め込みURL' ENGINE=InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci ;