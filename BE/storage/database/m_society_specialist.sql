DROP TABLE IF EXISTS `m_society_specialist`;

CREATE TABLE `m_society_specialist` (
   `person_cd` varchar(15) NOT NULL COMMENT '個人コード',
   `specialist_cd` varchar(4) NOT NULL COMMENT '専門医コード',
   `specialist_type` varchar(1) NOT NULL COMMENT '専門医区分:1： 専門医、2： 認定医、3： 指導医',
   `publication_date` date NOT NULL COMMENT '掲載年月日',
   `specialist_flag` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '専門医フラグ',
   `specialist_publication_date` date DEFAULT NULL COMMENT '専門医掲載日',
   `certified_flag` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '認定医フラグ',
   `certified_publication_date` date DEFAULT NULL COMMENT '認定医掲載日',
   `instructor_flag` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '指導医フラグ',
   `instructor_publication_date` date DEFAULT NULL COMMENT '指導医掲載日',
   `created_by` varchar(15) NOT NULL COMMENT '登録者コード',
   `proxy_created_by` varchar(15) DEFAULT NULL COMMENT '登録者コード（代行ログイン者）',
   `created_at` datetime NOT NULL COMMENT '登録日時',
   `updated_by` varchar(15) NOT NULL COMMENT '更新者コード',
   `proxy_updated_by` varchar(15) DEFAULT NULL COMMENT '更新者コード（代行ログイン者コード）',
   `updated_at` datetime NOT NULL COMMENT '更新日時',
   constraint `m_society_specialist_PKC` PRIMARY KEY (`person_cd`,`specialist_cd`,`specialist_type`)
 ) COMMENT='所属学会専門医' ENGINE=InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci ;