DROP TABLE IF EXISTS `t_convention_expense_detail`;

CREATE TABLE `t_convention_expense_detail` (
   `convention_id` bigint(20) unsigned NOT NULL COMMENT '講演会ID',
   `expense_item_cd` varchar(3) NOT NULL COMMENT '経費項目CD',
   `plan_unit_price` decimal(8,0) DEFAULT NULL COMMENT '計画単価',
   `plan_quantity` int(4) DEFAULT NULL COMMENT '計画数量',
   `plan_amount` decimal(12,0) DEFAULT NULL COMMENT '計画金額',
   `result_unit_price` decimal(8,0) DEFAULT NULL COMMENT '結果単価',
   `result_quantity` int(4) DEFAULT NULL COMMENT '結果数量',
   `result_amount` decimal(12,0) DEFAULT NULL COMMENT '結果金額',
   `option_item_name` varchar(20) DEFAULT NULL COMMENT '任意項目名',
   `option_item_content` varchar(50) DEFAULT NULL COMMENT '任意項目内容',
   `created_by` varchar(15) NOT NULL COMMENT '登録者コード',
   `proxy_created_by` varchar(15) DEFAULT NULL COMMENT '登録者コード（代行ログイン者）',
   `created_at` datetime NOT NULL COMMENT '登録日時',
   `updated_by` varchar(15) NOT NULL COMMENT '更新者コード',
   `proxy_updated_by` varchar(15) DEFAULT NULL COMMENT '更新者コード（代行ログイン者コード）',
   `updated_at` datetime NOT NULL COMMENT '更新日時',
   constraint `t_convention_expense_detail_PKC` PRIMARY KEY (`convention_id`,`expense_item_cd`)
 ) COMMENT='講演会経費明細' ENGINE=InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci ;