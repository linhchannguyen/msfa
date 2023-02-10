DROP TABLE IF EXISTS `t_user_profile`;

CREATE TABLE `t_user_profile` (
   `user_cd` varchar(15) NOT NULL COMMENT 'ユーザコード',
   `profile_image_file_id` bigint(20) unsigned DEFAULT NULL COMMENT 'プロフィール画像ファイルID',
   `note_1` varchar(200) DEFAULT NULL COMMENT '特記1',
   `note_2` varchar(200) DEFAULT NULL COMMENT '特記2',
   `note_3` varchar(200) DEFAULT NULL COMMENT '特記3',
   `note_4` varchar(200) DEFAULT NULL COMMENT '特記4',
   `note_5` varchar(200) DEFAULT NULL COMMENT '特記5',
   `created_by` varchar(15) NOT NULL COMMENT '登録者コード',
   `proxy_created_by` varchar(15) DEFAULT NULL COMMENT '登録者コード（代行ログイン者）',
   `created_at` datetime NOT NULL COMMENT '登録日時',
   `updated_by` varchar(15) NOT NULL COMMENT '更新者コード',
   `proxy_updated_by` varchar(15) DEFAULT NULL COMMENT '更新者コード（代行ログイン者コード）',
   `updated_at` datetime NOT NULL COMMENT '更新日時',
   constraint `t_user_profile_PKC` PRIMARY KEY (`user_cd`)
 ) COMMENT='ユーザプロフィール:ユーザの個人設定データ' ENGINE=InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci ;