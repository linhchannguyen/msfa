DROP TABLE IF EXISTS `m_convention_status_item`;

CREATE TABLE `m_convention_status_item` (
   `status_item_cd` varchar(10) NOT NULL COMMENT '状況確認項目コード',
   `status_item_name` varchar(20) NOT NULL COMMENT '状況確認項目名',
   `sort_order` tinyint(6) unsigned DEFAULT NULL COMMENT '表示順',
   `other_input_flag` tinyint(1) unsigned NOT NULL COMMENT 'その他人数入力フラグ',
   `status_input_type` varchar(10) NOT NULL COMMENT '状況確認入力区分:10:チェックボックス、20:プルダウン',
   `created_by` varchar(15) NOT NULL COMMENT '登録者コード',
   `proxy_created_by` varchar(15) DEFAULT NULL COMMENT '登録者コード（代行ログイン者）',
   `created_at` datetime NOT NULL COMMENT '登録日時',
   `updated_by` varchar(15) NOT NULL COMMENT '更新者コード',
   `proxy_updated_by` varchar(15) DEFAULT NULL COMMENT '更新者コード（代行ログイン者コード）',
   `updated_at` datetime NOT NULL COMMENT '更新日時',
   constraint `m_convention_status_item_PKC` PRIMARY KEY (`status_item_cd`)
 ) COMMENT='講演会出席者状況確認項目マスタ' ENGINE=InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci ;