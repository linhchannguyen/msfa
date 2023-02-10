DROP TABLE IF EXISTS `t_briefing_product`;

CREATE TABLE `t_briefing_product` (
   `briefing_id` bigint(20) unsigned NOT NULL COMMENT '説明会ID',
   `product_cd` varchar(10) NOT NULL COMMENT '品目コード',
   `product_name` varchar(30) DEFAULT NULL COMMENT '品目名（当時）',
   `created_by` varchar(15) NOT NULL COMMENT '登録者コード',
   `proxy_created_by` varchar(15) DEFAULT NULL COMMENT '登録者コード（代行ログイン者）',
   `created_at` datetime NOT NULL COMMENT '登録日時',
   `updated_by` varchar(15) NOT NULL COMMENT '更新者コード',
   `proxy_updated_by` varchar(15) DEFAULT NULL COMMENT '更新者コード（代行ログイン者コード）',
   `updated_at` datetime NOT NULL COMMENT '更新日時',
   constraint `t_briefing_product_PKC` PRIMARY KEY (`briefing_id`,`product_cd`)
 ) COMMENT='説明会品目' ENGINE=InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci ;