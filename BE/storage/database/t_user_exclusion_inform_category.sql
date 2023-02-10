DROP TABLE IF EXISTS `t_user_exclusion_inform_category`;

CREATE TABLE `t_user_exclusion_inform_category` (
   `user_cd` varchar(15) NOT NULL COMMENT 'ユーザコード',
   `inform_category_cd` varchar(5) NOT NULL COMMENT '通知種別コード',
   `created_by` varchar(15) NOT NULL COMMENT '登録者コード',
   `proxy_created_by` varchar(15) DEFAULT NULL COMMENT '登録者コード（代行ログイン者）',
   `created_at` datetime NOT NULL COMMENT '登録日時',
   `updated_by` varchar(15) NOT NULL COMMENT '更新者コード',
   `proxy_updated_by` varchar(15) DEFAULT NULL COMMENT '更新者コード（代行ログイン者コード）',
   `updated_at` datetime NOT NULL COMMENT '更新日時',
   constraint `t_user_exclusion_inform_category_PKC` PRIMARY KEY (`user_cd`,`inform_category_cd`)
 ) COMMENT='ユーザ通知除外管理' ENGINE=InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci ;