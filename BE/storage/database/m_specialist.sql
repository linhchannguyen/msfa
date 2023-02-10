DROP TABLE IF EXISTS `m_specialist`;

CREATE TABLE `m_specialist` (
  `specialist_cd` varchar(4) NOT NULL COMMENT '専門医コード',
  `specialist_name` varchar(50) NOT NULL COMMENT '専門医名',
  `created_by` varchar(15) NOT NULL COMMENT '登録者コード',
  `proxy_created_by` varchar(15) DEFAULT NULL COMMENT '登録者コード（代行ログイン者）',
  `created_at` datetime NOT NULL COMMENT '登録日時',
  `updated_by` varchar(15) NOT NULL COMMENT '更新者コード',
  `proxy_updated_by` varchar(15) DEFAULT NULL COMMENT '更新者コード（代行ログイン者コード）',
  `updated_at` datetime NOT NULL COMMENT '更新日時',
  constraint `m_specialist_PKC` PRIMARY KEY (`specialist_cd`)
) COMMENT='専門医マスタ:アルトマークのマスタ' ENGINE=InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci ;