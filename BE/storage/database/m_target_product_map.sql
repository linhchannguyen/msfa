DROP TABLE IF EXISTS `m_target_product_map`;

CREATE TABLE `m_target_product_map` (
  `target_product_cd` varchar(10) NOT NULL COMMENT 'ターゲット品目コード',
  `product_cd` varchar(10) NOT NULL COMMENT '品目コード',
  `created_by` varchar(15) NOT NULL COMMENT '登録者コード',
  `proxy_created_by` varchar(15) DEFAULT NULL COMMENT '登録者コード（代行ログイン者）',
  `created_at` datetime NOT NULL COMMENT '登録日時',
  `updated_by` varchar(15) NOT NULL COMMENT '更新者コード',
  `proxy_updated_by` varchar(15) DEFAULT NULL COMMENT '更新者コード（代行ログイン者コード）',
  `updated_at` datetime NOT NULL COMMENT '更新日時',
  constraint `m_target_product_map_PKC` PRIMARY KEY (`target_product_cd`,`product_cd`)
) COMMENT='ターゲット品目内訳' ENGINE=InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci ;