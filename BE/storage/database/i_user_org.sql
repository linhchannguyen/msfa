DROP TABLE IF EXISTS `i_user_org`;

CREATE TABLE `i_user_org` (
   `user_cd` varchar(15) NOT NULL COMMENT 'ユーザコード',
   `org_cd` varchar(20) NOT NULL COMMENT '組織コード',
   `start_date` date NOT NULL COMMENT '開始日付',
   `end_date` date NOT NULL COMMENT '終了日付',
   `user_post_type` varchar(2) DEFAULT NULL COMMENT 'ユーザ役職区分',
   `main_flag` tinyint(1) unsigned NOT NULL COMMENT '主所属フラグ:1:主勤務先（ログイン時に取得する）\r\n0:兼務先',
   `created_by` varchar(15) NOT NULL COMMENT '登録者コード',
   `proxy_created_by` varchar(15) DEFAULT NULL COMMENT '登録者コード（代行ログイン者）',
   `created_at` datetime NOT NULL COMMENT '登録日時',
   `updated_by` varchar(15) NOT NULL COMMENT '更新者コード',
   `proxy_updated_by` varchar(15) DEFAULT NULL COMMENT '更新者コード（代行ログイン者コード）',
   `updated_at` datetime NOT NULL COMMENT '更新日時',
   constraint `i_user_org_PKC` PRIMARY KEY (`user_cd`,`org_cd`,`start_date`)
 ) COMMENT='ユーザ組織マスタ（履歴）' ENGINE=InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci ;