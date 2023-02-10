DROP TABLE IF EXISTS `t_private`;

CREATE TABLE `t_private` (
   `private_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'プライベートID',
   `schedule_id` bigint(20) unsigned DEFAULT NULL COMMENT 'スケジュールID',
   `title` varchar(30) DEFAULT NULL COMMENT 'タイトル',
   `remarks` varchar(200) DEFAULT NULL COMMENT '備考',
   `created_by` varchar(15) NOT NULL COMMENT '登録者コード',
   `proxy_created_by` varchar(15) DEFAULT NULL COMMENT '登録者コード（代行ログイン者）',
   `created_at` datetime NOT NULL COMMENT '登録日時',
   `updated_by` varchar(15) NOT NULL COMMENT '更新者コード',
   `proxy_updated_by` varchar(15) DEFAULT NULL COMMENT '更新者コード（代行ログイン者コード）',
   `updated_at` datetime NOT NULL COMMENT '更新日時',
   constraint `t_private_PKC` PRIMARY KEY (`private_id`)
 ) COMMENT='プライベート' ENGINE=InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci ;

create index `tes_user`
  on `t_private`(`title`);