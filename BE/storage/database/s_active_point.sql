DROP TABLE IF EXISTS `s_active_point`;

CREATE TABLE `s_active_point` (
   `user_cd` varchar(15) NOT NULL COMMENT 'ユーザコード',
   `point_target_type` varchar(3) NOT NULL COMMENT 'ポイント対象区分',
   `active_point` int(11) NOT NULL COMMENT 'ポイント',
   `created_by` varchar(15) NOT NULL COMMENT '登録者コード',
   `proxy_created_by` varchar(15) DEFAULT NULL COMMENT '登録者コード（代行ログイン者）',
   `created_at` datetime NOT NULL COMMENT '登録日時',
   `updated_by` varchar(15) NOT NULL COMMENT '更新者コード',
   `proxy_updated_by` varchar(15) DEFAULT NULL COMMENT '更新者コード（代行ログイン者コード）',
   `updated_at` datetime NOT NULL COMMENT '更新日時',
   constraint `s_active_point_PKC` PRIMARY KEY (`user_cd`,`point_target_type`)
 ) COMMENT='活用度ポイント集計' ENGINE=InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci ;