DROP TABLE IF EXISTS `m_knowledge_category`;

CREATE TABLE `m_knowledge_category` (
   `knowledge_category_cd` varchar(3) NOT NULL COMMENT 'カテゴリコード',
   `knowledge_category_name` varchar(30) NOT NULL COMMENT 'カテゴリ名',
   `sort_order` tinyint(6) unsigned NOT NULL COMMENT '表示順',
   `delete_flag` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '削除フラグ',
   `created_by` varchar(15) NOT NULL COMMENT '登録者コード',
   `proxy_created_by` varchar(15) DEFAULT NULL COMMENT '登録者コード（代行ログイン者）',
   `created_at` datetime NOT NULL COMMENT '登録日時',
   `updated_by` varchar(15) NOT NULL COMMENT '更新者コード',
   `proxy_updated_by` varchar(15) DEFAULT NULL COMMENT '更新者コード（代行ログイン者コード）',
   `updated_at` datetime NOT NULL COMMENT '更新日時',
   constraint `m_knowledge_category_PKC` PRIMARY KEY (`knowledge_category_cd`)
 ) COMMENT='ナレッジカテゴリマスタ:ナレッジのカテゴリマスタ' ENGINE=InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci ;