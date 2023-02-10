DROP TABLE IF EXISTS `m_facility_type`;

CREATE TABLE `m_facility_type` (
   `facility_type` varchar(2) NOT NULL COMMENT '施設区分',
   `facility_type_name` varchar(100) NOT NULL COMMENT '施設区分名',
   `facility_type_group` varchar(2) NOT NULL COMMENT '施設区分グループ',
   `created_by` varchar(15) NOT NULL COMMENT '登録者コード',
   `proxy_created_by` varchar(15) DEFAULT NULL COMMENT '登録者コード（代行ログイン者）',
   `created_at` datetime NOT NULL COMMENT '登録日時',
   `updated_by` varchar(15) NOT NULL COMMENT '更新者コード',
   `proxy_updated_by` varchar(15) DEFAULT NULL COMMENT '更新者コード（代行ログイン者コード）',
   `updated_at` datetime NOT NULL COMMENT '更新日時',
   constraint `m_facility_type_PKC` PRIMARY KEY (`facility_type`)
 ) COMMENT='施設区分マスタ' ENGINE=InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci ;