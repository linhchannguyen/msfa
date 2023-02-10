DROP TABLE IF EXISTS `t_unsuitable_report`;

CREATE TABLE `t_unsuitable_report` (
   `unsuitable_report_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '不適切報告ID',
   `key_id` bigint(20) unsigned NOT NULL COMMENT '対象ID:質問IDまたは回答ID',
   `key_type` varchar(2) NOT NULL COMMENT '対象区分:不適切報告対象の区分。10:質問、20:回答',
   `create_user_cd` varchar(15) NOT NULL COMMENT 'ユーザコード（作成者）',
   `create_datetime` datetime NOT NULL COMMENT '作成日時',
   `comment` varchar(100) NOT NULL COMMENT '報告コメント',
   `cancel_flag` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '解除フラグ:1:削除',
   `created_by` varchar(15) NOT NULL COMMENT '登録者コード',
   `proxy_created_by` varchar(15) DEFAULT NULL COMMENT '登録者コード（代行ログイン者）',
   `created_at` datetime NOT NULL COMMENT '登録日時',
   `updated_by` varchar(15) NOT NULL COMMENT '更新者コード',
   `proxy_updated_by` varchar(15) DEFAULT NULL COMMENT '更新者コード（代行ログイン者コード）',
   `updated_at` datetime NOT NULL COMMENT '更新日時',
   constraint `t_unsuitable_report_PKC` PRIMARY KEY (`unsuitable_report_id`)
 ) COMMENT='QA不適切報告:QAの質問・回答の不適切投稿を管理者に報告するデータ' ENGINE=InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci ;