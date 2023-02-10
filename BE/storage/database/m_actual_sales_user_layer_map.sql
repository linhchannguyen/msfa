DROP TABLE IF EXISTS `m_actual_sales_user_layer_map`;

CREATE TABLE `m_actual_sales_user_layer_map` (
   `user_cd` varchar(15) NOT NULL COMMENT 'ユーザコード',
   `aggregate_layer_num` tinyint(1) NOT NULL COMMENT '集計階層',
   `aggregate_layer_cd` varchar(30) NOT NULL COMMENT '集計階層コード:階層に応じて組織コードかユーザコードをセットする',
   `created_by` varchar(15) NOT NULL COMMENT '登録者コード',
   `proxy_created_by` varchar(15) DEFAULT NULL COMMENT '登録者コード（代行ログイン者）',
   `created_at` datetime NOT NULL COMMENT '登録日時',
   `updated_by` varchar(15) NOT NULL COMMENT '更新者コード',
   `proxy_updated_by` varchar(15) DEFAULT NULL COMMENT '更新者コード（代行ログイン者コード）',
   `updated_at` datetime NOT NULL COMMENT '更新日時',
   constraint `m_actual_sales_user_layer_map_PKC` PRIMARY KEY (`user_cd`,`aggregate_layer_num`)
 ) COMMENT='実消化ユーザ階層マップ' ENGINE=InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci ;