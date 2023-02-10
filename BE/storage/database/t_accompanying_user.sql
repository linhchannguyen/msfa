DROP TABLE IF EXISTS `t_accompanying_user`;

CREATE TABLE `t_accompanying_user` (
   `accompanying_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '同行者ID',
   `visit_id` bigint(20) unsigned DEFAULT NULL COMMENT '訪問ID',
   `user_cd` varchar(15) NOT NULL COMMENT 'ユーザコード（同行者）',
   `user_name` varchar(30) NOT NULL COMMENT 'ユーザ名（同行者当時）',
   `org_cd` varchar(20) DEFAULT NULL COMMENT '組織コード（同行者当時）',
   `org_short_name` varchar(30) DEFAULT NULL COMMENT '組織ラベル（同行者当時）',
   `created_by` varchar(15) NOT NULL COMMENT '登録者コード',
   `proxy_created_by` varchar(15) DEFAULT NULL COMMENT '登録者コード（代行ログイン者）',
   `created_at` datetime NOT NULL COMMENT '登録日時',
   `updated_by` varchar(15) NOT NULL COMMENT '更新者コード',
   `proxy_updated_by` varchar(15) DEFAULT NULL COMMENT '更新者コード（代行ログイン者コード）',
   `updated_at` datetime NOT NULL COMMENT '更新日時',
   constraint `t_accompanying_user_PKC` PRIMARY KEY (`accompanying_id`)
 ) COMMENT='社内同行者' ENGINE=InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci ;

create index `tes_user`
  on `t_accompanying_user`(`user_cd`);