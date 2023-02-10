DROP TABLE IF EXISTS `c_screen`;

CREATE TABLE `c_screen` (
  `screen_cd` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '画面コード',
  `component` varchar(255) NOT NULL COMMENT 'コンポーネント',
  `screen_name` varchar(255) NOT NULL COMMENT '画面名',
  `menu_cd` int(11) DEFAULT NULL COMMENT 'メニューコード',
  `url` text NOT NULL COMMENT 'URL',
  `pc_visible_flag` tinyint(1) NOT NULL COMMENT 'ＰＣ表示フラグ',
  `tablet_visible_flag` tinyint(1) NOT NULL COMMENT 'タブレット表示フラグ',
  `smartphone_visible_flag` tinyint(1) NOT NULL COMMENT 'スマートフォン表示フラグ',
  `sort_order` int(6) NOT NULL DEFAULT 1 COMMENT '表示順',
  `created_by` varchar(15) NOT NULL COMMENT '登録者コード',
  `proxy_created_by` varchar(15) DEFAULT NULL COMMENT '登録者コード（代行ログイン者）',
  `created_at` datetime NOT NULL COMMENT '登録日時',
  `updated_by` varchar(15) NOT NULL COMMENT '更新者コード',
  `proxy_updated_by` varchar(15) DEFAULT NULL COMMENT '更新者コード（代行ログイン者コード）',
  `updated_at` datetime NOT NULL COMMENT '更新日時',
  constraint `c_screen_PKC` PRIMARY KEY (`screen_cd`)
) COMMENT = '画面' ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;