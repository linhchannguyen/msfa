DROP TABLE IF EXISTS `m_convention_status_item_select`;

CREATE TABLE `m_convention_status_item_select` (
   `status_item_cd` varchar(10) NOT NULL COMMENT '状況確認項目コード',
   `select_cd` varchar(10) NOT NULL COMMENT '選択肢コード',
   `select_label` varchar(20) NOT NULL COMMENT '選択肢ラベル',
   `output_select_label` varchar(20) NOT NULL COMMENT '帳票出力ラベル',
   `sort_order` tinyint(6) unsigned DEFAULT NULL COMMENT '表示順',
   `created_by` varchar(15) NOT NULL COMMENT '登録者コード',
   `proxy_created_by` varchar(15) DEFAULT NULL COMMENT '登録者コード（代行ログイン者）',
   `created_at` datetime NOT NULL COMMENT '登録日時',
   `updated_by` varchar(15) NOT NULL COMMENT '更新者コード',
   `proxy_updated_by` varchar(15) DEFAULT NULL COMMENT '更新者コード（代行ログイン者コード）',
   `updated_at` datetime NOT NULL COMMENT '更新日時',
   constraint `m_convention_status_item_select_PKC` PRIMARY KEY (`status_item_cd`,`select_cd`)
 ) COMMENT='講演会出席者状況確認項目選択肢マスタ' ENGINE=InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;
