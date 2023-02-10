DROP TABLE IF EXISTS `m_display_option`;

CREATE TABLE `m_display_option` (
   `display_option_type` varchar(3) NOT NULL COMMENT '表示形式',
   `display_option_name` varchar(15) NOT NULL COMMENT '表示形式名',
   `background_color` varchar(7) NOT NULL COMMENT '背景色',
   `icon` varchar(255) NOT NULL COMMENT 'アイコン:データ型はOMI様と要相談',
   `frame_border_color` varchar(7) NOT NULL COMMENT '枠線色',
   `font_color` varchar(7) NOT NULL COMMENT '文字色',
   `created_by` varchar(15) NOT NULL COMMENT '登録者コード',
   `proxy_created_by` varchar(15) DEFAULT NULL COMMENT '登録者コード（代行ログイン者）',
   `created_at` datetime NOT NULL COMMENT '登録日時',
   `updated_by` varchar(15) NOT NULL COMMENT '更新者コード',
   `proxy_updated_by` varchar(15) DEFAULT NULL COMMENT '更新者コード（代行ログイン者コード）',
   `updated_at` datetime NOT NULL COMMENT '更新日時',
   constraint `m_display_option_PKC` PRIMARY KEY (`display_option_type`)
 ) COMMENT='表示形式マスタ' ENGINE=InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci ;