DROP TABLE IF EXISTS `h_enclave_user`;

CREATE TABLE `h_enclave_user` (
  `facility_cd` varchar(25) NOT NULL COMMENT '施設コード',
  `user_cd` varchar(15) NOT NULL COMMENT 'ユーザコード',
  `start_date` date NOT NULL COMMENT '開始日付',
  `end_date` date DEFAULT NULL COMMENT '終了日付',
  `main_flag` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT 'メインフラグ:0:副担当、1:主担当',
  `created_by` varchar(15) NOT NULL COMMENT '登録者コード',
  `proxy_created_by` varchar(15) DEFAULT NULL COMMENT '登録者コード（代行ログイン者）',
  `created_at` datetime NOT NULL COMMENT '登録日時',
  `updated_by` varchar(15) NOT NULL COMMENT '更新者コード',
  `proxy_updated_by` varchar(15) DEFAULT NULL COMMENT '更新者コード（代行ログイン者コード）',
  `updated_at` datetime NOT NULL COMMENT '更新日時',
  constraint `h_enclave_user_PKC` PRIMARY KEY (`facility_cd`,`user_cd`,`start_date`)
) COMMENT='飛び地担当マスタ（履歴）:飛び地担当マスタ（履歴）' ENGINE=InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci ;

create index `mcu_user_facility`
  on `h_enclave_user`(`user_cd`,`facility_cd`);


