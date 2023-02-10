DROP TABLE IF EXISTS `t_briefing_expense_detail`;

CREATE TABLE `t_briefing_expense_detail` (
   `briefing_id` bigint(20) unsigned NOT NULL COMMENT '説明会ID',
   `expense_item_cd` varchar(3) NOT NULL COMMENT '経費項目CD',
   `plan_unit_price` decimal(8,0) DEFAULT NULL COMMENT '計画単価',
   `plan_quantity` int(4) DEFAULT NULL COMMENT '計画数量',
   `plan_amount` decimal(8,0) DEFAULT NULL COMMENT '計画金額',
   `result_unit_price` decimal(8,0) DEFAULT NULL COMMENT '結果単価',
   `result_quantity` int(4) DEFAULT NULL COMMENT '結果数量',
   `result_amount` decimal(8,0) DEFAULT NULL COMMENT '結果金額',
   `created_by` varchar(15) NOT NULL COMMENT '登録者コード',
   `proxy_created_by` varchar(15) DEFAULT NULL COMMENT '登録者コード（代行ログイン者）',
   `created_at` datetime NOT NULL COMMENT '登録日時',
   `updated_by` varchar(15) NOT NULL COMMENT '更新者コード',
   `proxy_updated_by` varchar(15) DEFAULT NULL COMMENT '更新者コード（代行ログイン者コード）',
   `updated_at` datetime NOT NULL COMMENT '更新日時',
   constraint `t_briefing_expense_detail_PKC` PRIMARY KEY (`briefing_id`,`expense_item_cd`)
 ) COMMENT='説明会経費明細' ENGINE=InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci ;
