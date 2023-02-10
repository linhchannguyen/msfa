DROP TABLE IF EXISTS `m_target_product`;

CREATE TABLE `m_target_product` (
   `target_product_cd` varchar(10) NOT NULL COMMENT 'ターゲット品目コード',
   `target_product_name` varchar(30) NOT NULL COMMENT 'ターゲット品目名',
   `start_date` date NOT NULL COMMENT '適用期間開始日',
   `end_date` date NOT NULL COMMENT '適用期間終了日',
   `selection_start_date` date DEFAULT NULL COMMENT '選定期間開始日',
   `selection_end_date` date DEFAULT NULL COMMENT '選定期間終了日',
   `sort_order` tinyint(6) unsigned NOT NULL COMMENT '表示順',
   `delete_flag` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '削除フラグ',
   `created_by` varchar(15) NOT NULL COMMENT '登録者コード',
   `proxy_created_by` varchar(15) DEFAULT NULL COMMENT '登録者コード（代行ログイン者）',
   `created_at` datetime NOT NULL COMMENT '登録日時',
   `updated_by` varchar(15) NOT NULL COMMENT '更新者コード',
   `proxy_updated_by` varchar(15) DEFAULT NULL COMMENT '更新者コード（代行ログイン者コード）',
   `updated_at` datetime NOT NULL COMMENT '更新日時',
   constraint `m_target_product_PKC` PRIMARY KEY (`target_product_cd`)
 ) COMMENT='ターゲット品目マスタ'  ENGINE=InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci ;