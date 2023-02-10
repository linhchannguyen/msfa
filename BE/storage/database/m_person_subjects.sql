DROP TABLE IF EXISTS `m_person_subjects`;

CREATE TABLE `m_person_subjects` (
   `person_cd` varchar(15) NOT NULL COMMENT '個人コード',
   `medical_subjects_cd` varchar(3) NOT NULL COMMENT '診療科目コード',
   `created_by` varchar(15) NOT NULL COMMENT '登録者コード',
   `proxy_created_by` varchar(15) DEFAULT NULL COMMENT '登録者コード（代行ログイン者）',
   `created_at` datetime NOT NULL COMMENT '登録日時',
   `updated_by` varchar(15) NOT NULL COMMENT '更新者コード',
   `proxy_updated_by` varchar(15) DEFAULT NULL COMMENT '更新者コード（代行ログイン者コード）',
   `updated_at` datetime NOT NULL COMMENT '更新日時',
   constraint `m_person_subjects_PKC` PRIMARY KEY (`person_cd`,`medical_subjects_cd`)
 ) COMMENT='個人診療科目:アルトマークのマスタ' ENGINE=InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci ;