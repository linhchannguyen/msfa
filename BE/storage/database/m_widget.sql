DROP TABLE IF EXISTS `m_widget`;

CREATE TABLE `m_widget` (
   `widget_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ウィジェットID',
   `widget_type` varchar(3) NOT NULL COMMENT 'ウィジェット種別',
   `widget_type_id` bigint(20) unsigned DEFAULT NULL COMMENT 'ウィジェット種別ID:ウィジェットの具体的な内容に紐づけるためのID',
   `widget_title` varchar(30) NOT NULL COMMENT 'ウィジェットタイトル:ウィジェットのヘッダーに表示するタイトル',
   `sort_order` tinyint(6) unsigned NOT NULL COMMENT '表示順',
   `height` int(11) NOT NULL COMMENT '高さ',
   `width` int(11) NOT NULL COMMENT '幅',
   `created_by` varchar(15) NOT NULL COMMENT '登録者コード',
   `proxy_created_by` varchar(15) DEFAULT NULL COMMENT '登録者コード（代行ログイン者）',
   `created_at` datetime NOT NULL COMMENT '登録日時',
   `updated_by` varchar(15) NOT NULL COMMENT '更新者コード',
   `proxy_updated_by` varchar(15) DEFAULT NULL COMMENT '更新者コード（代行ログイン者コード）',
   `updated_at` datetime NOT NULL COMMENT '更新日時',
   constraint `m_widget_PKC` PRIMARY KEY (`widget_id`)
 ) COMMENT='ウィジェット' ENGINE=InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci ;