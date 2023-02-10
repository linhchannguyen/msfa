DROP TABLE IF EXISTS `t_document_review`;

CREATE TABLE `t_document_review` (
   `usage_situation_id` bigint(20) unsigned NOT NULL COMMENT '使用状況ID',
   `document_id` bigint(20) unsigned NOT NULL COMMENT '資材ID',
   `document_review` tinyint(1) NOT NULL COMMENT '資材評価',
   `review_customize_document_id` bigint(20) unsigned DEFAULT NULL COMMENT '評価先カスタム資材ID',
   `review_comment` text DEFAULT NULL COMMENT '評価コメント',
   `review_datetime` datetime NOT NULL COMMENT '評価日時',
   `review_org_label` varchar(50) NOT NULL COMMENT '組織ラベル(評価者当時)',
   `review_user_cd` varchar(15) NOT NULL COMMENT 'ユーザコード（評価者当時）',
   `review_user_name` varchar(50) NOT NULL COMMENT 'ユーザ名（評価者当時）',
   `created_by` varchar(15) NOT NULL COMMENT '登録者コード',
   `proxy_created_by` varchar(15) DEFAULT NULL COMMENT '登録者コード（代行ログイン者）',
   `created_at` datetime NOT NULL COMMENT '登録日時',
   `updated_by` varchar(15) NOT NULL COMMENT '更新者コード',
   `proxy_updated_by` varchar(15) DEFAULT NULL COMMENT '更新者コード（代行ログイン者コード）',
   `updated_at` datetime NOT NULL COMMENT '更新日時',
   constraint `t_document_review_PKC` PRIMARY KEY (`usage_situation_id`)
 ) COMMENT='資材評価' ENGINE=InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci ;