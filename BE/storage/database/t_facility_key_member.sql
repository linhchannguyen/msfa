DROP TABLE IF EXISTS `t_facility_key_member`;

CREATE TABLE `t_facility_key_member` (
   `facility_cd` varchar(25) NOT NULL COMMENT '施設コード',
   `person_cd` varchar(15) NOT NULL COMMENT '個人コード',
   `part_type` varchar(2) NOT NULL COMMENT '役割区分:10：薬審メンバー',
   `last_updated_at` date DEFAULT NULL COMMENT '最終更新日付',
   `created_by` varchar(15) NOT NULL COMMENT '登録者コード',
   `proxy_created_by` varchar(15) DEFAULT NULL COMMENT '登録者コード（代行ログイン者）',
   `created_at` datetime NOT NULL COMMENT '登録日時',
   `updated_by` varchar(15) NOT NULL COMMENT '更新者コード',
   `proxy_updated_by` varchar(15) DEFAULT NULL COMMENT '更新者コード（代行ログイン者コード）',
   `updated_at` datetime NOT NULL COMMENT '更新日時',
   constraint `t_facility_key_member_PKC` PRIMARY KEY (`facility_cd`,`person_cd`,`part_type`)
 ) COMMENT='施設主要メンバー' ENGINE=InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci ;