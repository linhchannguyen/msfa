DROP TABLE IF EXISTS `m_facility_department`;

CREATE TABLE `m_facility_department` (
   `facility_cd` varchar(25) NOT NULL COMMENT '施設コード',
   `department_cd` varchar(4) NOT NULL COMMENT '所属部科コード',
   `created_by` varchar(15) NOT NULL COMMENT '登録者コード',
   `proxy_created_by` varchar(15) DEFAULT NULL COMMENT '登録者コード（代行ログイン者）',
   `created_at` datetime NOT NULL COMMENT '登録日時',
   `updated_by` varchar(15) NOT NULL COMMENT '更新者コード',
   `proxy_updated_by` varchar(15) DEFAULT NULL COMMENT '更新者コード（代行ログイン者コード）',
   `updated_at` datetime NOT NULL COMMENT '更新日時',
   constraint `m_facility_department_PKC` PRIMARY KEY (`facility_cd`,`department_cd`)
 ) COMMENT='施設所属部科:アルトマークのマスタ' ENGINE=InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci ;