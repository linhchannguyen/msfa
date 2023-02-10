DROP TABLE IF EXISTS `h_approval_user`;

CREATE TABLE `h_approval_user` (
   `approval_setting_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '設定ID',
   `request_user_cd` varchar(15) NOT NULL COMMENT 'ユーザコード（申請者）',
   `approval_work_type` varchar(3) NOT NULL COMMENT '承認業務区分:0:日報 | 1:説明会 | 2:講演会 | 3:ナレッジ',
   `approval_layer_num` tinyint(1) NOT NULL COMMENT '承認階層:1,2,3,4,...',
   `approval_user_cd` varchar(15) DEFAULT NULL COMMENT 'ユーザコード（承認者）',
   `start_date` date DEFAULT NULL COMMENT '適用開始日',
   `end_date` date DEFAULT NULL COMMENT '適用終了日',
   `created_by` varchar(15) NOT NULL COMMENT '登録者コード',
   `proxy_created_by` varchar(15) DEFAULT NULL COMMENT '登録者コード（代行ログイン者）',
   `created_at` datetime NOT NULL COMMENT '登録日時',
   `updated_by` varchar(15) NOT NULL COMMENT '更新者コード',
   `proxy_updated_by` varchar(15) DEFAULT NULL COMMENT '更新者コード（代行ログイン者コード）',
   `updated_at` datetime NOT NULL COMMENT '更新日時',
   constraint `h_approval_user_PKC` PRIMARY KEY (`approval_setting_id`)
 ) COMMENT='承認者設定(履歴)' ENGINE=InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci ;