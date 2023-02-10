DROP TABLE IF EXISTS `c_screen_policy`;

CREATE TABLE `c_screen_policy` (
  `screen_cd` int(11) NOT NULL COMMENT '画面コード:画面コード',
  `role` varchar(5) NOT NULL COMMENT 'ロール:ロール',
  `created_by` varchar(15) NOT NULL COMMENT '登録者コード',
  `proxy_created_by` varchar(15) DEFAULT NULL COMMENT '登録者コード（代行ログイン者）',
  `created_at` datetime NOT NULL COMMENT '登録日時',
  `updated_by` varchar(15) NOT NULL COMMENT '更新者コード',
  `proxy_updated_by` varchar(15) DEFAULT NULL COMMENT '更新者コード（代行ログイン者コード）',
  `updated_at` datetime NOT NULL COMMENT '更新日時',
  constraint `c_screen_policy_PKC` PRIMARY KEY (`screen_cd`,`role`)
) COMMENT='画面ポリシー' ENGINE=InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci ;