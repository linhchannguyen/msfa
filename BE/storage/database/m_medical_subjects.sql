DROP TABLE IF EXISTS `m_medical_subjects`;

CREATE TABLE `m_medical_subjects` (
   `medical_subjects_cd` varchar(3) NOT NULL COMMENT '診療科目コード',
   `medical_subjects_group_cd` varchar(3) DEFAULT NULL COMMENT '診療科目分類コード',
   `medical_subjects_name` varchar(40) NOT NULL COMMENT '診療科目名',
   `medical_subjects_name_kana` varchar(45) DEFAULT NULL COMMENT '診療科目ｶﾅ',
   `medical_subjects_short_name` varchar(4) DEFAULT NULL COMMENT '診療科目略名',
   `created_by` varchar(15) NOT NULL COMMENT '登録者コード',
   `proxy_created_by` varchar(15) DEFAULT NULL COMMENT '登録者コード（代行ログイン者）',
   `created_at` datetime NOT NULL COMMENT '登録日時',
   `updated_by` varchar(15) NOT NULL COMMENT '更新者コード',
   `proxy_updated_by` varchar(15) DEFAULT NULL COMMENT '更新者コード（代行ログイン者コード）',
   `updated_at` datetime NOT NULL COMMENT '更新日時',
   constraint `m_medical_subjects_PKC` PRIMARY KEY (`medical_subjects_cd`)
 ) COMMENT='診療科目マスタ' ENGINE=InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci ;