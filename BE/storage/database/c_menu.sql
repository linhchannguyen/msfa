DROP TABLE IF EXISTS `c_menu`;

CREATE TABLE `c_menu` (
  `menu_cd` int(11) NOT NULL COMMENT 'メニューグループコード',
  `menu_name` varchar(255) NOT NULL COMMENT 'メニューグループ名',
  `sort_order` int(6) NOT NULL COMMENT '表示順',
  `created_by` varchar(15) NOT NULL COMMENT '登録者コード',
  `proxy_created_by` varchar(15) DEFAULT NULL COMMENT '登録者コード（代行ログイン者）',
  `created_at` datetime NOT NULL COMMENT '登録日時',
  `updated_by` varchar(15) NOT NULL COMMENT '更新者コード',
  `proxy_updated_by` varchar(15) DEFAULT NULL COMMENT '更新者コード（代行ログイン者コード）',
  `updated_at` datetime NOT NULL COMMENT '更新日時',
  constraint `c_menu_PKC` PRIMARY KEY (`menu_cd`)
) COMMENT='メニュー' ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE = utf8mb4_general_ci ;