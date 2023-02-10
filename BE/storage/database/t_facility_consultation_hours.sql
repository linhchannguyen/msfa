DROP TABLE IF EXISTS `t_facility_consultation_hours`;

CREATE TABLE `t_facility_consultation_hours` (
   `facility_cd` varchar(25) NOT NULL COMMENT '施設コード',
   `consultation_hours_note` varchar(2000) DEFAULT NULL COMMENT '診療時間',
   `created_by` varchar(15) NOT NULL COMMENT '登録者コード',
   `proxy_created_by` varchar(15) DEFAULT NULL COMMENT '登録者コード（代行ログイン者）',
   `created_at` datetime NOT NULL COMMENT '登録日時',
   `updated_by` varchar(15) NOT NULL COMMENT '更新者コード',
   `proxy_updated_by` varchar(15) DEFAULT NULL COMMENT '更新者コード（代行ログイン者コード）',
   `updated_at` datetime NOT NULL COMMENT '更新日時',
   constraint `t_facility_consultation_hours_PKC` PRIMARY KEY (`facility_cd`)
 ) COMMENT='施設診療時間' ENGINE=InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci ;