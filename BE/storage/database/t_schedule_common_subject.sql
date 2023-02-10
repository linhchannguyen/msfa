DROP TABLE IF EXISTS `t_schedule_common_subject`;

CREATE TABLE `t_schedule_common_subject` (
   `common_subject_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '共有対象ID',
   `schedule_id` bigint(20) unsigned DEFAULT NULL COMMENT 'スケジュールID',
   `org_cd` varchar(20) DEFAULT NULL COMMENT '組織コード',
   `user_cd` varchar(15) DEFAULT NULL COMMENT 'ユーザコード:講演会はダミーコードをセットする',
   `created_by` varchar(15) NOT NULL COMMENT '登録者コード',
   `proxy_created_by` varchar(15) DEFAULT NULL COMMENT '登録者コード（代行ログイン者）',
   `created_at` datetime NOT NULL COMMENT '登録日時',
   `updated_by` varchar(15) NOT NULL COMMENT '更新者コード',
   `proxy_updated_by` varchar(15) DEFAULT NULL COMMENT '更新者コード（代行ログイン者コード）',
   `updated_at` datetime NOT NULL COMMENT '更新日時',
   constraint `t_schedule_common_subject_PKC` PRIMARY KEY (`common_subject_id`)
 ) COMMENT='スケジュール共有対象' ENGINE=InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

create index `tes_user`
  on `t_schedule_common_subject`(`org_cd`);