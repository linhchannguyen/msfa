DROP TABLE IF EXISTS `t_question_imformation_org`;

CREATE TABLE `t_question_imformation_org` (
   `question_id` bigint(20) unsigned NOT NULL COMMENT '質問ID',
   `org_cd` varchar(20) NOT NULL COMMENT '組織コード',
   `created_by` varchar(15) NOT NULL COMMENT '登録者コード',
   `proxy_created_by` varchar(15) DEFAULT NULL COMMENT '登録者コード（代行ログイン者）',
   `created_at` datetime NOT NULL COMMENT '登録日時',
   `updated_by` varchar(15) NOT NULL COMMENT '更新者コード',
   `proxy_updated_by` varchar(15) DEFAULT NULL COMMENT '更新者コード（代行ログイン者コード）',
   `updated_at` datetime NOT NULL COMMENT '更新日時',
   constraint `t_question_imformation_org_PKC` PRIMARY KEY (`question_id`,`org_cd`)
 ) COMMENT='QA通知先:QA質問の通知先組織' ENGINE=InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci ;