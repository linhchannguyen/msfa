DROP TABLE IF EXISTS `t_answer`;

CREATE TABLE `t_answer` (
   `answer_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '回答ID',
   `question_id` bigint(20) unsigned NOT NULL COMMENT '質問ID',
   `create_user_cd` varchar(15) NOT NULL COMMENT 'ユーザコード（作成者）',
   `answer_note` text NOT NULL COMMENT '回答本文',
   `best_answer_flag` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT 'ベストアンサーフラグ:1:ベストアンサー',
   `last_update_datetime` datetime NOT NULL COMMENT '最終更新日時',
   `delete_flag` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '削除フラグ:1:削除',
   `created_by` varchar(15) NOT NULL COMMENT '登録者コード',
   `proxy_created_by` varchar(15) DEFAULT NULL COMMENT '登録者コード（代行ログイン者）',
   `created_at` datetime NOT NULL COMMENT '登録日時',
   `updated_by` varchar(15) NOT NULL COMMENT '更新者コード',
   `proxy_updated_by` varchar(15) DEFAULT NULL COMMENT '更新者コード（代行ログイン者コード）',
   `updated_at` datetime NOT NULL COMMENT '更新日時',
   constraint `t_answer_PKC` PRIMARY KEY (`answer_id`)
 ) COMMENT='QA回答:QAの回答データ' ENGINE=InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci ;