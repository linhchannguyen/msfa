DROP TABLE IF EXISTS `m_user_org`;

CREATE TABLE `m_user_org` (
   `user_cd` varchar(15) NOT NULL COMMENT 'ユーザコード',
   `org_cd` varchar(20) NOT NULL COMMENT '組織コード',
   `user_post_type` varchar(2) DEFAULT NULL COMMENT 'ユーザ役職区分',
   `main_flag` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '主所属フラグ',
   `created_by` varchar(15) NOT NULL COMMENT '登録者コード',
   `proxy_created_by` varchar(15) DEFAULT NULL COMMENT '登録者コード（代行ログイン者）',
   `created_at` datetime NOT NULL COMMENT '登録日時',
   `updated_by` varchar(15) NOT NULL COMMENT '更新者コード',
   `proxy_updated_by` varchar(15) DEFAULT NULL COMMENT '更新者コード（代行ログイン者コード）',
   `updated_at` datetime NOT NULL COMMENT '更新日時',
   constraint `m_user_org_PKC` PRIMARY KEY (`user_cd`,`org_cd`)
 ) COMMENT='ユーザ組織マスタ' ENGINE=InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci ;