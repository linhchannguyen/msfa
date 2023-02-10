DROP TABLE IF EXISTS `t_visit`;

CREATE TABLE `t_visit` (
   `visit_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '訪問ID',
   `schedule_id` bigint(20) unsigned NOT NULL COMMENT 'スケジュールID',
   `important_flag` tinyint(1) unsigned DEFAULT NULL COMMENT '重要フラグ',
   `facility_cd` varchar(25) DEFAULT NULL COMMENT '施設コード',
   `facility_short_name` varchar(50) DEFAULT NULL COMMENT '施設略名（当時）',
   `remarks` varchar(200) DEFAULT NULL COMMENT '備考',
   `user_cd` varchar(15) NOT NULL COMMENT 'ユーザコード（入力者）',
   `user_name` varchar(50) NOT NULL COMMENT 'ユーザ名（入力者当時）',
   `implement_user_post` varchar(2) DEFAULT NULL COMMENT 'ユーザ役職区分（実施者当時）',
   `implement_user_post_name` varchar(100) DEFAULT NULL COMMENT 'ユーザ役職名（実施者当時）',
   `org_cd` varchar(20) NOT NULL COMMENT '組織コード（入力者当時）',
   `org_short_name` varchar(200) NOT NULL COMMENT '組織ラベル（入力者当時）',
   `created_by` varchar(15) NOT NULL COMMENT '登録者コード',
   `proxy_created_by` varchar(15) DEFAULT NULL COMMENT '登録者コード（代行ログイン者）',
   `created_at` datetime NOT NULL COMMENT '登録日時',
   `updated_by` varchar(15) NOT NULL COMMENT '更新者コード',
   `proxy_updated_by` varchar(15) DEFAULT NULL COMMENT '更新者コード（代行ログイン者コード）',
   `updated_at` datetime NOT NULL COMMENT '更新日時',
   constraint `t_visit_PKC` PRIMARY KEY (`visit_id`)
 ) COMMENT='訪問' ENGINE=InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

create index `tv_event_visit`
  on `t_visit`(`visit_id`,`schedule_id`);