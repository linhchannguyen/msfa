DROP TABLE IF EXISTS `m_watch_user_color`;

CREATE TABLE `m_watch_user_color` (
   `display_color_cd` varchar(3) NOT NULL COMMENT '表示色コード',
   `display_color` varchar(7) NOT NULL COMMENT '表示色',
   `memo` varchar(255) DEFAULT NULL COMMENT 'メモ:作業者が色を認識するためもメモ書きとして使用',
   `sort_order` tinyint(6) unsigned DEFAULT NULL COMMENT '表示順',
   `created_by` varchar(15) NOT NULL COMMENT '登録者コード',
   `proxy_created_by` varchar(15) DEFAULT NULL COMMENT '登録者コード（代行ログイン者）',
   `created_at` datetime NOT NULL COMMENT '登録日時',
   `updated_by` varchar(15) NOT NULL COMMENT '更新者コード',
   `proxy_updated_by` varchar(15) DEFAULT NULL COMMENT '更新者コード（代行ログイン者コード）',
   `updated_at` datetime NOT NULL COMMENT '更新日時',
   constraint `m_watch_user_color_PKC` PRIMARY KEY (`display_color_cd`)
 ) COMMENT='ウォッチユーザ表示色マスタ' ENGINE=InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci ;