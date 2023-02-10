DROP TABLE IF EXISTS `m_facility_type_group`;

CREATE TABLE `m_facility_type_group` (
   `facility_type_group_cd` varchar(3) NOT NULL COMMENT '施設区分分類コード',
   `facility_type_group_name` varchar(20) DEFAULT NULL COMMENT '施設区分分類名',
   `created_by` varchar(15) NOT NULL COMMENT '登録者コード',
   `proxy_created_by` varchar(15) DEFAULT NULL COMMENT '登録者コード（代行ログイン者）',
   `created_at` datetime NOT NULL COMMENT '登録日時',
   `updated_by` varchar(15) NOT NULL COMMENT '更新者コード',
   `proxy_updated_by` varchar(15) DEFAULT NULL COMMENT '更新者コード（代行ログイン者コード）',
   `updated_at` datetime NOT NULL COMMENT '更新日時',
   constraint `m_facility_type_group_PKC` PRIMARY KEY (`facility_type_group_cd`)
 ) COMMENT='施設区分分類マスタ' ENGINE=InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci ;