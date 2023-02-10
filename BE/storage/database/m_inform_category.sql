DROP TABLE IF EXISTS `m_inform_category`;

CREATE TABLE `m_inform_category` (
   `inform_category_cd` varchar(5) NOT NULL COMMENT '通知種別コード',
   `inform_category_name` varchar(20) NOT NULL COMMENT '通知種別名',
   `inform_stop_flag` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '通知停止フラグ',
   `sort_order` tinyint(1) unsigned NOT NULL COMMENT '表示順',
   `created_by` varchar(15) NOT NULL COMMENT '登録者コード',
   `proxy_created_by` varchar(15) DEFAULT NULL COMMENT '登録者コード（代行ログイン者）',
   `created_at` datetime NOT NULL COMMENT '登録日時',
   `updated_by` varchar(15) NOT NULL COMMENT '更新者コード',
   `proxy_updated_by` varchar(15) DEFAULT NULL COMMENT '更新者コード（代行ログイン者コード）',
   `updated_at` datetime NOT NULL COMMENT '更新日時',
   constraint `m_inform_category_PKC` PRIMARY KEY (`inform_category_cd`)
 ) COMMENT='通知種別マスタ' ENGINE=InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci ;