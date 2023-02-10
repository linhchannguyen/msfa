DROP TABLE IF EXISTS `m_briefing_expense_item`;

CREATE TABLE `m_briefing_expense_item` (
   `expense_item_cd` varchar(3) NOT NULL COMMENT '経費項目CD',
   `expense_item_name` varchar(20) NOT NULL COMMENT '経費項目名',
   `sort_order` tinyint(6) unsigned DEFAULT NULL COMMENT '表示順',
   `unit_price_input_flag` tinyint(1) unsigned NOT NULL COMMENT '単価入力フラグ',
   `quantity_input_flag` tinyint(1) unsigned NOT NULL COMMENT '数量入力フラグ',
   `quantity_unit_type` varchar(2) DEFAULT NULL COMMENT '数量単位区分',
   `start_date` date NOT NULL COMMENT '開始日付',
   `end_date` date NOT NULL COMMENT '終了日付',
   `created_by` varchar(15) NOT NULL COMMENT '登録者コード',
   `proxy_created_by` varchar(15) DEFAULT NULL COMMENT '登録者コード（代行ログイン者）',
   `created_at` datetime NOT NULL COMMENT '登録日時',
   `updated_by` varchar(15) NOT NULL COMMENT '更新者コード',
   `proxy_updated_by` varchar(15) DEFAULT NULL COMMENT '更新者コード（代行ログイン者コード）',
   `updated_at` datetime NOT NULL COMMENT '更新日時',
   constraint `m_briefing_expense_item_PKC` PRIMARY KEY (`expense_item_cd`)
 ) COMMENT='説明会経費項目' ENGINE=InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci ;