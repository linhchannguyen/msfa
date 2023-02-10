DROP TABLE IF EXISTS `h_facility_person`;

CREATE TABLE `h_facility_person` (
   `person_cd` varchar(15) NOT NULL COMMENT '個人コード',
   `facility_cd` varchar(25) NOT NULL COMMENT '施設コード',
   `start_date` date NOT NULL COMMENT '開始日付',
   `end_date` date NOT NULL COMMENT '終了日付',
   `department_cd` varchar(4) DEFAULT NULL COMMENT '所属部科コード',
   `position_cd` varchar(3) DEFAULT NULL COMMENT '施設役職コード',
   `academic_position_cd` varchar(3) DEFAULT NULL COMMENT '大学職位コード',
   `created_by` varchar(15) NOT NULL COMMENT '登録者コード',
   `proxy_created_by` varchar(15) DEFAULT NULL COMMENT '登録者コード（代行ログイン者）',
   `created_at` datetime NOT NULL COMMENT '登録日時',
   `updated_by` varchar(15) NOT NULL COMMENT '更新者コード',
   `proxy_updated_by` varchar(15) DEFAULT NULL COMMENT '更新者コード（代行ログイン者コード）',
   `updated_at` datetime NOT NULL COMMENT '更新日時',
   constraint `h_facility_person_PKC` PRIMARY KEY (`person_cd`,`facility_cd`,`start_date`)
 ) COMMENT='勤務履歴' ENGINE=InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci ;