DROP TABLE IF EXISTS `m_convention_expense_item`;

CREATE TABLE `m_convention_expense_item` (
   `expense_item_cd` varchar(3) NOT NULL COMMENT '経費項目CD',
   `expense_item_name` varchar(20) DEFAULT NULL COMMENT '経費項目名',
   `layer_num` tinyint(1) DEFAULT NULL COMMENT '経費項目階層:1,2,3',
   `sort_order` tinyint(6) unsigned DEFAULT NULL COMMENT '表示順',
   `parent_expense_item_id` bigint(20) unsigned DEFAULT NULL COMMENT '親経費項目ID',
   `unit_price_input_flag` tinyint(1) unsigned NOT NULL COMMENT '単価入力フラグ',
   `quantity_input_flag` tinyint(1) unsigned NOT NULL COMMENT '数量入力フラグ',
   `quantity_unit_type` varchar(2) DEFAULT NULL COMMENT '数量単位区分',
   `option_item_flag` tinyint(1) unsigned NOT NULL COMMENT '任意項目フラグ',
   `start_date` date NOT NULL COMMENT '開始日付',
   `end_date` date NOT NULL COMMENT '終了日付',
   `created_by` varchar(15) NOT NULL COMMENT '登録者コード',
   `proxy_created_by` varchar(15) DEFAULT NULL COMMENT '登録者コード（代行ログイン者）',
   `created_at` datetime NOT NULL COMMENT '登録日時',
   `updated_by` varchar(15) NOT NULL COMMENT '更新者コード',
   `proxy_updated_by` varchar(15) DEFAULT NULL COMMENT '更新者コード（代行ログイン者コード）',
   `updated_at` datetime NOT NULL COMMENT '更新日時',
   constraint `m_convention_expense_item_PKC` PRIMARY KEY (`expense_item_cd`)
 ) COMMENT='講演会経費項目' ENGINE=InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci ;