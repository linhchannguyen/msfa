DROP TABLE IF EXISTS `t_select_product`;

CREATE TABLE `t_select_product` (
   `user_cd` varchar(15) NOT NULL COMMENT 'ユーザコード',
   `product_cd` varchar(10) NOT NULL COMMENT '品目コード',
   `created_by` varchar(15) NOT NULL COMMENT '登録者コード',
   `proxy_created_by` varchar(15) DEFAULT NULL COMMENT '登録者コード（代行ログイン者）',
   `created_at` datetime NOT NULL COMMENT '登録日時',
   `updated_by` varchar(15) NOT NULL COMMENT '更新者コード',
   `proxy_updated_by` varchar(15) DEFAULT NULL COMMENT '更新者コード（代行ログイン者コード）',
   `updated_at` datetime NOT NULL COMMENT '更新日時',
   constraint `t_select_product_PKC` PRIMARY KEY (`user_cd`,`product_cd`)
 ) COMMENT='セレクト品目' ENGINE=InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci ;