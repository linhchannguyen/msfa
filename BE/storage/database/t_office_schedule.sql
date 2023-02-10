DROP TABLE IF EXISTS `t_office_schedule`;

CREATE TABLE `t_office_schedule` (
   `office_schedule_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '社内予定ID',
   `schedule_id` bigint(20) unsigned DEFAULT NULL COMMENT 'スケジュールID',
   `office_schedule_type` varchar(3) DEFAULT NULL COMMENT '社内予定区分',
   `title` varchar(30) DEFAULT NULL COMMENT 'タイトル',
   `remarks` varchar(200) DEFAULT NULL COMMENT '備考',
   `created_by` varchar(15) NOT NULL COMMENT '登録者コード',
   `proxy_created_by` varchar(15) DEFAULT NULL COMMENT '登録者コード（代行ログイン者）',
   `created_at` datetime NOT NULL COMMENT '登録日時',
   `updated_by` varchar(15) NOT NULL COMMENT '更新者コード',
   `proxy_updated_by` varchar(15) DEFAULT NULL COMMENT '更新者コード（代行ログイン者コード）',
   `updated_at` datetime NOT NULL COMMENT '更新日時',
   constraint `t_office_schedule_PKC` PRIMARY KEY (`office_schedule_id`)
 ) COMMENT='社内予定' ENGINE=InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci ;

create index `tes_user`
  on `t_office_schedule`(`title`);