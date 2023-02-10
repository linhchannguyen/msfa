DROP TABLE IF EXISTS `t_message`;

CREATE TABLE `t_message` (
   `message_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'メッセージID',
   `message_subject` varchar(30) NOT NULL COMMENT '件名',
   `release_start_date` date NOT NULL COMMENT '掲載期間開始日',
   `release_end_date` date NOT NULL COMMENT '掲載期間終了日',
   `release_org_cd` varchar(20) DEFAULT NULL COMMENT '表示対象組織コード',
   `sender_name` varchar(50) NOT NULL COMMENT '発信者',
   `message_contents` mediumtext NOT NULL COMMENT '本文',
   `important_flag` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '重要フラグ',
   `last_update_datetime` datetime NOT NULL COMMENT '最終更新日時',
   `create_user_cd` varchar(15) NOT NULL COMMENT 'ユーザコード（作成者）',
   `created_by` varchar(15) NOT NULL COMMENT '登録者コード',
   `proxy_created_by` varchar(15) DEFAULT NULL COMMENT '登録者コード（代行ログイン者）',
   `created_at` datetime NOT NULL COMMENT '登録日時',
   `updated_by` varchar(15) NOT NULL COMMENT '更新者コード',
   `proxy_updated_by` varchar(15) DEFAULT NULL COMMENT '更新者コード（代行ログイン者コード）',
   `updated_at` datetime NOT NULL COMMENT '更新日時',
   constraint `t_message_PKC` PRIMARY KEY (`message_id`)
 ) COMMENT='メッセージ' ENGINE=InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci ;