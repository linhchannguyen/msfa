DROP TABLE IF EXISTS `h_area_user`;

CREATE TABLE `h_area_user` (
   `prefecture_cd` varchar(2) NOT NULL COMMENT '都道府県コード',
   `municipality_cd` varchar(3) NOT NULL COMMENT '市区町村コード',
   `user_cd` varchar(15) NOT NULL COMMENT 'ユーザコード',
   `start_date` date NOT NULL COMMENT '開始日付',
   `end_date` date DEFAULT NULL COMMENT '終了日付',
   `main_flg` tinyint(1) unsigned DEFAULT NULL COMMENT 'メインフラグ',
   `created_by` varchar(15) NOT NULL COMMENT '登録者コード',
   `proxy_created_by` varchar(15) DEFAULT NULL COMMENT '登録者コード（代行ログイン者）',
   `created_at` datetime NOT NULL COMMENT '登録日時',
   `updated_by` varchar(15) NOT NULL COMMENT '更新者コード',
   `proxy_updated_by` varchar(15) DEFAULT NULL COMMENT '更新者コード（代行ログイン者コード）',
   `updated_at` datetime NOT NULL COMMENT '更新日時',
   constraint `h_area_user_PKC` PRIMARY KEY (`prefecture_cd`,`municipality_cd`,`user_cd`,`start_date`)
 ) COMMENT='地区担当マスタ（履歴）' ENGINE=InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci ;
