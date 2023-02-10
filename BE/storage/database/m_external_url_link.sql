DROP TABLE IF EXISTS `m_external_url_link`;

CREATE TABLE `m_external_url_link` (
   `external_url_link_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '外部URLリンクID',
   `external_url_link_name` varchar(100) NOT NULL COMMENT '外部URLリンク名',
   `external_url` varchar(200) NOT NULL COMMENT '外部URL',
   `sort_order` tinyint(6) unsigned NOT NULL COMMENT '表示順',
   `created_by` varchar(15) NOT NULL COMMENT '登録者コード',
   `proxy_created_by` varchar(15) DEFAULT NULL COMMENT '登録者コード（代行ログイン者）',
   `created_at` datetime NOT NULL COMMENT '登録日時',
   `updated_by` varchar(15) NOT NULL COMMENT '更新者コード',
   `proxy_updated_by` varchar(15) DEFAULT NULL COMMENT '更新者コード（代行ログイン者コード）',
   `updated_at` datetime NOT NULL COMMENT '更新日時',
   constraint `m_external_url_link_PKC` PRIMARY KEY (`external_url_link_id`)
 ) COMMENT='外部URLリンク' ENGINE=InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci ;