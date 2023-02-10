DROP TABLE IF EXISTS `m_widget_type`;

CREATE TABLE `m_widget_type` (
   `widget_type` varchar(3) NOT NULL COMMENT 'ウィジェット種別',
   `created_by` varchar(15) NOT NULL COMMENT '登録者コード',
   `proxy_created_by` varchar(15) DEFAULT NULL COMMENT '登録者コード（代行ログイン者）',
   `created_at` datetime NOT NULL COMMENT '登録日時',
   `updated_by` varchar(15) NOT NULL COMMENT '更新者コード',
   `proxy_updated_by` varchar(15) DEFAULT NULL COMMENT '更新者コード（代行ログイン者コード）',
   `updated_at` datetime NOT NULL COMMENT '更新日時',
   constraint `m_widget_type_PKC` PRIMARY KEY (`widget_type`)
 ) COMMENT='ウィジェット種別' ENGINE=InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci ;