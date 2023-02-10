DROP TABLE IF EXISTS `i_layer_actual_sales_process`;

CREATE TABLE `i_layer_actual_sales_process` (
   `aggregate_layer_cd` varchar(30) NOT NULL COMMENT '集計階層コード',
   `actual_sales_product_cd` varchar(20) NOT NULL COMMENT '実消化品目コード',
   `sales_amount` decimal(10,2) NOT NULL COMMENT '売上金額',
   `goal_amount` decimal(10,2) NOT NULL COMMENT '目標金額',
   `created_by` varchar(15) NOT NULL COMMENT '登録者コード',
   `proxy_created_by` varchar(15) DEFAULT NULL COMMENT '登録者コード（代行ログイン者）',
   `created_at` datetime NOT NULL COMMENT '登録日時',
   `updated_by` varchar(15) NOT NULL COMMENT '更新者コード',
   `proxy_updated_by` varchar(15) DEFAULT NULL COMMENT '更新者コード（代行ログイン者コード）',
   `updated_at` datetime NOT NULL COMMENT '更新日時',
   constraint `i_layer_actual_sales_process_PKC` PRIMARY KEY (`aggregate_layer_cd`,`actual_sales_product_cd`)
 ) COMMENT='階層別実消化進捗サマリ' ENGINE=InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci ;