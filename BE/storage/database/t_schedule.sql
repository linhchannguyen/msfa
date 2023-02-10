DROP TABLE IF EXISTS `t_schedule`;

CREATE TABLE `t_schedule` (
   `schedule_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'スケジュールID',
   `schedule_type` varchar(2) NOT NULL COMMENT 'スケジュール区分:10:面談, 20:社内予定, 30:プライベート, 40:説明会, 50:講演会',
   `start_date` date NOT NULL COMMENT '活動日付',
   `start_time` datetime NOT NULL COMMENT '開始日時',
   `end_time` datetime NOT NULL COMMENT '終了日時',
   `title` varchar(100) DEFAULT NULL COMMENT 'タイトル:説明会、講演会、社内予定、プライベートで設定される',
   `all_day_flag` tinyint(1) unsigned NOT NULL COMMENT '終日フラグ',
   `display_option_type` varchar(3) NOT NULL DEFAULT '0' COMMENT '表示形式:面談、講演会、説明会の実施前や実施後の状態を持つ',
   `user_cd` varchar(15) DEFAULT NULL COMMENT 'ユーザコード（入力者）',
   `non_display_flag` tinyint(1) NOT NULL DEFAULT 0 COMMENT '非表示フラグ:非表示フラグ：中止された講演会・説明会対しては「1」にセット、スケジュール上で表示しない`',
   `created_by` varchar(15) NOT NULL COMMENT '登録者コード',
   `proxy_created_by` varchar(15) DEFAULT NULL COMMENT '登録者コード（代行ログイン者）',
   `created_at` datetime NOT NULL COMMENT '登録日時',
   `updated_by` varchar(15) NOT NULL COMMENT '更新者コード',
   `proxy_updated_by` varchar(15) DEFAULT NULL COMMENT '更新者コード（代行ログイン者コード）',
   `updated_at` datetime NOT NULL COMMENT '更新日時',
   constraint `t_accompanying_user_PKC` PRIMARY KEY (`schedule_id`)
 ) COMMENT='スケジュール' ENGINE=InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci ;

create index `ts_user_date`
  on `t_schedule`(`user_cd`,`start_date`);
