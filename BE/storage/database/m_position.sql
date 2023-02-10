DROP TABLE IF EXISTS `m_position`;

CREATE TABLE `m_position` (
  `position_cd` varchar(3) NOT NULL COMMENT '役職コード',
  `position_name` varchar(25) NOT NULL COMMENT '役職名称',
  `created_by` varchar(15) NOT NULL COMMENT '登録者コード',
  `proxy_created_by` varchar(15) DEFAULT NULL COMMENT '登録者コード（代行ログイン者）',
  `created_at` datetime NOT NULL COMMENT '登録日時',
  `updated_by` varchar(15) NOT NULL COMMENT '更新者コード',
  `proxy_updated_by` varchar(15) DEFAULT NULL COMMENT '更新者コード（代行ログイン者コード）',
  `updated_at` datetime NOT NULL COMMENT '更新日時',
  constraint `m_position_PKC` PRIMARY KEY (`position_cd`)
) COMMENT='役職マスタ:アルトマークのマスタ\r\n院内役職・大学職位のマスタ' ENGINE=InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci ;