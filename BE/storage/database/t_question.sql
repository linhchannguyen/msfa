DROP TABLE IF EXISTS `t_question`;

CREATE TABLE `t_question` (
   `question_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '質問ID',
   `question_category_cd` varchar(3) NOT NULL COMMENT 'カテゴリコード',
   `question_status_type` varchar(2) NOT NULL DEFAULT '10' COMMENT 'ステータス区分:10:回答受付中、20:終了',
   `create_user_cd` varchar(15) NOT NULL COMMENT 'ユーザコード（作成者）',
   `title` varchar(30) NOT NULL COMMENT 'タイトル',
   `contents` text NOT NULL COMMENT '質問本文',
   `last_update_datetime` datetime NOT NULL COMMENT '最終更新日時',
   `delete_flag` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '削除フラグ:1:削除',
   `created_by` varchar(15) NOT NULL COMMENT '登録者コード',
   `proxy_created_by` varchar(15) DEFAULT NULL COMMENT '登録者コード（代行ログイン者）',
   `created_at` datetime NOT NULL COMMENT '登録日時',
   `updated_by` varchar(15) NOT NULL COMMENT '更新者コード',
   `proxy_updated_by` varchar(15) DEFAULT NULL COMMENT '更新者コード（代行ログイン者コード）',
   `updated_at` datetime NOT NULL COMMENT '更新日時',
   constraint `t_question_PKC` PRIMARY KEY (`question_id`)
 ) COMMENT='QA質問:QAの質問データ' ENGINE=InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci ;