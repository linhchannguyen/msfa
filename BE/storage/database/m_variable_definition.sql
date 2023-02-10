DROP TABLE IF EXISTS `m_variable_definition`;

CREATE TABLE `m_variable_definition` (
   `definition_name` varchar(20) NOT NULL COMMENT '定義名',
   `definition_value` varchar(100) NOT NULL COMMENT '定義値',
   `definition_label` varchar(100) DEFAULT NULL COMMENT '表示名',
   `remarks` varchar(200) DEFAULT NULL COMMENT '備考',
   `sort_order` tinyint(6) unsigned DEFAULT NULL COMMENT '表示順',
   `created_by` varchar(15) NOT NULL COMMENT '登録者コード',
   `proxy_created_by` varchar(15) DEFAULT NULL COMMENT '登録者コード（代行ログイン者）',
   `created_at` datetime NOT NULL COMMENT '登録日時',
   `updated_by` varchar(15) NOT NULL COMMENT '更新者コード',
   `proxy_updated_by` varchar(15) DEFAULT NULL COMMENT '更新者コード（代行ログイン者コード）',
   `updated_at` datetime NOT NULL COMMENT '更新日時',
   constraint `m_variable_definition_PKC` PRIMARY KEY (`definition_name`,`definition_value`)
 ) COMMENT='変数定義マスタ:専用マスタ作る程のものではないもの、システム全体の共通文言など（有無のあり/なしとか）、変わる可能性が極めて低いものを管理する共通マスタ。' ENGINE=InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci ;