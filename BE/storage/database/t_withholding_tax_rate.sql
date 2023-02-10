DROP TABLE IF EXISTS `t_withholding_tax_rate`;

CREATE TABLE `t_withholding_tax_rate` (
   `tax_rate_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '源泉取得税率ID',
   `min_amount` decimal(8,0) NOT NULL COMMENT '所得金額下限',
   `max_amount` decimal(8,0) NOT NULL COMMENT '所得金額上限',
   `tax_rate` decimal(3,2) NOT NULL COMMENT '税率',
   `start_date` date NOT NULL COMMENT '開始日付',
   `end_date` date NOT NULL COMMENT '終了日付',
   `created_by` varchar(15) NOT NULL COMMENT '登録者コード',
   `proxy_created_by` varchar(15) DEFAULT NULL COMMENT '登録者コード（代行ログイン者）',
   `created_at` datetime NOT NULL COMMENT '登録日時',
   `updated_by` varchar(15) NOT NULL COMMENT '更新者コード',
   `proxy_updated_by` varchar(15) DEFAULT NULL COMMENT '更新者コード（代行ログイン者コード）',
   `updated_at` datetime NOT NULL COMMENT '更新日時',
   constraint `t_withholding_tax_rate_PKC` PRIMARY KEY (`tax_rate_id`)
 ) COMMENT='源泉取得税率マスタ' ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci ;