DROP TABLE IF EXISTS `m_society_member`;

CREATE TABLE `m_society_member` (
  `person_cd` varchar(15) NOT NULL COMMENT '個人コード',
  `society_cd` varchar(4) NOT NULL COMMENT '学会コード',
  `entry_year` varchar(4) NOT NULL COMMENT '学会年度',
  `delete_flag` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '削除フラグ',
  `created_by` varchar(15) NOT NULL COMMENT '登録者コード',
  `proxy_created_by` varchar(15) DEFAULT NULL COMMENT '登録者コード（代行ログイン者）',
  `created_at` datetime NOT NULL COMMENT '登録日時',
  `updated_by` varchar(15) NOT NULL COMMENT '更新者コード',
  `proxy_updated_by` varchar(15) DEFAULT NULL COMMENT '更新者コード（代行ログイン者コード）',
  `updated_at` datetime NOT NULL COMMENT '更新日時',
  constraint `m_society_member_PKC` PRIMARY KEY (`person_cd`,`society_cd`,`entry_year`)
) COMMENT='所属学会' ENGINE=InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci ;