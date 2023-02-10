DROP TABLE IF EXISTS `t_person_medical_office`;

CREATE TABLE `t_person_medical_office` (
   `person_cd` varchar(15) NOT NULL COMMENT '個人コード',
   `medical_office_facility_cd` varchar(25) NOT NULL COMMENT '施設コード（医局大学）',
   `medical_office_department_cd` varchar(4) DEFAULT NULL COMMENT '所属部科コード（医局）',
   `medical_office_name` varchar(100) DEFAULT NULL COMMENT '医局名',
   `created_by` varchar(15) NOT NULL COMMENT '登録者コード',
   `proxy_created_by` varchar(15) DEFAULT NULL COMMENT '登録者コード（代行ログイン者）',
   `created_at` datetime NOT NULL COMMENT '登録日時',
   `updated_by` varchar(15) NOT NULL COMMENT '更新者コード',
   `proxy_updated_by` varchar(15) DEFAULT NULL COMMENT '更新者コード（代行ログイン者コード）',
   `updated_at` datetime NOT NULL COMMENT '更新日時',
   constraint `t_person_medical_office_PKC` PRIMARY KEY (`person_cd`)
 ) COMMENT='個人医局:個人の出身医局' ENGINE=InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci ;