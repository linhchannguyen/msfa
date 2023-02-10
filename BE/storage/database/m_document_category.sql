DROP TABLE IF EXISTS `m_document_category`;

CREATE TABLE `m_document_category` (
   `document_category_cd` varchar(3) NOT NULL COMMENT '資材種別コード',
   `document_category_name` varchar(10) NOT NULL COMMENT '資材種別名',
   `cover_flag` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '表紙・目次フラグ',
   `sort_order` tinyint(6) unsigned DEFAULT NULL COMMENT '表示順',
   `created_by` varchar(15) NOT NULL COMMENT '登録者コード',
   `proxy_created_by` varchar(15) DEFAULT NULL COMMENT '登録者コード（代行ログイン者）',
   `created_at` datetime NOT NULL COMMENT '登録日時',
   `updated_by` varchar(15) NOT NULL COMMENT '更新者コード',
   `proxy_updated_by` varchar(15) DEFAULT NULL COMMENT '更新者コード（代行ログイン者コード）',
   `updated_at` datetime NOT NULL COMMENT '更新日時',
   constraint `m_document_category_PKC` PRIMARY KEY (`document_category_cd`)
 ) COMMENT='資材種別マスタ' ENGINE=InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci ;