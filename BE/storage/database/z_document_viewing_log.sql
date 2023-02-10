DROP TABLE IF EXISTS `z_document_viewing_log`;

CREATE TABLE `z_document_viewing_log` (
   `document_viewing_log_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '視聴ログID',
   `document_id` bigint(20) unsigned NOT NULL COMMENT '資材ID',
   `schedule_type` varchar(3) DEFAULT NULL COMMENT 'スケジュール区分',
   `schedule_id` bigint(20) unsigned DEFAULT NULL COMMENT 'スケジュールID',
   `schedule_detail_id` bigint(20) unsigned DEFAULT NULL COMMENT 'スケジュール詳細ID:スケジュールIDの中で直接資材に紐づくID\r\n活動：ディテールID\r\n説明会：説明会ID\r\n講演会：講演会ID',
   `total_viewing_start_datetime` datetime NOT NULL COMMENT '総合視聴開始時間',
   `total_viewing_end_datetime` datetime NOT NULL COMMENT '総合視聴終了時間',
   `total_viewing_time` time NOT NULL COMMENT '総合視聴時間',
   `usage_user_cd` varchar(15) NOT NULL COMMENT 'ユーザコード(使用者)',
   `created_by` varchar(15) NOT NULL COMMENT '登録者コード',
   `proxy_created_by` varchar(15) DEFAULT NULL COMMENT '登録者コード（代行ログイン者）',
   `created_at` datetime NOT NULL COMMENT '登録日時',
   `updated_by` varchar(15) NOT NULL COMMENT '更新者コード',
   `proxy_updated_by` varchar(15) DEFAULT NULL COMMENT '更新者コード（代行ログイン者コード）',
   `updated_at` datetime NOT NULL COMMENT '更新日時',
   constraint `z_document_viewing_log_PKC` PRIMARY KEY (`document_viewing_log_id`)
 ) COMMENT='資材視聴ログ' ENGINE=InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci ;