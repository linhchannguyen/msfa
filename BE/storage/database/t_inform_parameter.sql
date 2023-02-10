DROP TABLE IF EXISTS `t_inform_parameter`;

CREATE TABLE `t_inform_parameter` (
   `inform_id` bigint(20) unsigned NOT NULL COMMENT '通知ID',
   `parameter_key` varchar(20) NOT NULL COMMENT 'パラメータキー',
   `parameter_value` varchar(150) NOT NULL COMMENT 'パラメータ値',
   `created_by` varchar(15) NOT NULL COMMENT '登録者コード',
   `proxy_created_by` varchar(15) DEFAULT NULL COMMENT '登録者コード（代行ログイン者）',
   `created_at` datetime NOT NULL COMMENT '登録日時',
   `updated_by` varchar(15) NOT NULL COMMENT '更新者コード',
   `proxy_updated_by` varchar(15) DEFAULT NULL COMMENT '更新者コード（代行ログイン者コード）',
   `updated_at` datetime NOT NULL COMMENT '更新日時',
   constraint `t_inform_parameter_PKC` PRIMARY KEY (`inform_id`,`parameter_key`)
 ) COMMENT='通知遷移パラメータ' ENGINE=InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci ;