DROP TABLE IF EXISTS `c_role`;

CREATE TABLE `c_role` (
  `role` varchar(5) NOT NULL COMMENT 'ロール',
  `role_name` varchar(20) DEFAULT NULL COMMENT 'ロール名',
  `role_short_name` varchar(10) DEFAULT NULL COMMENT 'ロール略名',
  `memo` varchar(200) DEFAULT NULL COMMENT 'メモ',
  `created_by` varchar(15) NOT NULL COMMENT '登録者コード',
  `proxy_created_by` varchar(15) DEFAULT NULL COMMENT '登録者コード（代行ログイン者）',
  `created_at` datetime NOT NULL COMMENT '登録日時',
  `updated_by` varchar(15) NOT NULL COMMENT '更新者コード',
  `proxy_updated_by` varchar(15) DEFAULT NULL COMMENT '更新者コード（代行ログイン者コード）',
  `updated_at` datetime NOT NULL COMMENT '更新日時',
  constraint `c_role_PKC` PRIMARY KEY (`role`)
) COMMENT='ロール' ENGINE=InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci ;