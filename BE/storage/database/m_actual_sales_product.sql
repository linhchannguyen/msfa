DROP TABLE IF EXISTS `m_actual_sales_product`;

CREATE TABLE `m_actual_sales_product` (
   `actual_sales_product_cd` varchar(20) NOT NULL COMMENT '実消化品目コード',
   `actual_sales_product_name` varchar(30) NOT NULL COMMENT '実消化品目名',
   `sort_order` tinyint(6) unsigned NOT NULL COMMENT '表示順',
   `created_by` varchar(15) NOT NULL COMMENT '登録者コード',
   `proxy_created_by` varchar(15) DEFAULT NULL COMMENT '登録者コード（代行ログイン者）',
   `created_at` datetime NOT NULL COMMENT '登録日時',
   `updated_by` varchar(15) NOT NULL COMMENT '更新者コード',
   `proxy_updated_by` varchar(15) DEFAULT NULL COMMENT '更新者コード（代行ログイン者コード）',
   `updated_at` datetime NOT NULL COMMENT '更新日時',
   constraint `m_actual_sales_product_PKC` PRIMARY KEY (`actual_sales_product_cd`)
 ) COMMENT='実消化品目マスタ' ENGINE=InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci ;