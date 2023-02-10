DROP TABLE IF EXISTS `m_product_group`;

CREATE TABLE `m_product_group` (
  `product_group_cd` varchar(10) NOT NULL COMMENT '品目分類コード',
  `product_group_name` varchar(30) NOT NULL COMMENT '品目分類名',
  `start_date` date NOT NULL COMMENT '開始日付',
  `end_date` date NOT NULL COMMENT '終了日付',
  `sort_no` tinyint(6) unsigned NOT NULL COMMENT '表示順',
  `created_by` varchar(15) NOT NULL COMMENT '登録者コード',
  `proxy_created_by` varchar(15) DEFAULT NULL COMMENT '登録者コード（代行ログイン者）',
  `created_at` datetime NOT NULL COMMENT '登録日時',
  `updated_by` varchar(15) NOT NULL COMMENT '更新者コード',
  `proxy_updated_by` varchar(15) DEFAULT NULL COMMENT '更新者コード（代行ログイン者コード）',
  `updated_at` datetime NOT NULL COMMENT '更新日時',
  constraint `m_product_group_PKC` PRIMARY KEY (`product_group_cd`,`start_date`)
) COMMENT='品目分類マスタ' ENGINE=InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci ;