DROP TABLE IF EXISTS `m_convention_occupation_type`;

CREATE TABLE `m_convention_occupation_type` (
   `occupation_type` varchar(10) NOT NULL COMMENT '職種区分',
   `occupation_name` varchar(20) NOT NULL COMMENT '職種名',
   `medical_staff_cd` varchar(15) DEFAULT NULL COMMENT '医療従事者区分',
   `sort_order` tinyint(6) unsigned DEFAULT NULL COMMENT '表示順',
   `created_by` varchar(15) NOT NULL COMMENT '登録者コード',
   `proxy_created_by` varchar(15) DEFAULT NULL COMMENT '登録者コード（代行ログイン者）',
   `created_at` datetime NOT NULL COMMENT '登録日時',
   `updated_by` varchar(15) NOT NULL COMMENT '更新者コード',
   `proxy_updated_by` varchar(15) DEFAULT NULL COMMENT '更新者コード（代行ログイン者コード）',
   `updated_at` datetime NOT NULL COMMENT '更新日時',
   constraint `m_convention_occupation_type_PKC` PRIMARY KEY (`occupation_type`)
 ) COMMENT='講演会出席者職種区分マスタ' ENGINE=InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci ;