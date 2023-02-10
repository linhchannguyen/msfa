DROP TABLE IF EXISTS `t_approval_request`;

CREATE TABLE `t_approval_request` (
 `request_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '申請ID',
 `request_type` varchar(3) NOT NULL COMMENT '申請区分:0:日報 | 1:説明会計画 | 2:説明会結果 | 3:講演会計画 | 4:講演会結果 | 5:ナレッジ',
 `request_target_id` bigint(20) unsigned NOT NULL COMMENT '申請機能ID:日報ID/説明会ID/講演会ID/ナレッジID',
 `active_layer_num` tinyint(1) NOT NULL COMMENT 'アクティブ承認階層',
 `request_user_cd` varchar(15) NOT NULL COMMENT 'ユーザコード（申請者）',
 `request_datetime` datetime NOT NULL COMMENT '申請日時',
 `status_type` varchar(3) NOT NULL COMMENT 'ステータス区分:0:承認待ち | 1:承認済み | 2:差戻',
 `created_by` varchar(15) NOT NULL COMMENT '登録者コード',
 `proxy_created_by` varchar(15) DEFAULT NULL COMMENT '登録者コード（代行ログイン者）',
 `created_at` datetime NOT NULL COMMENT '登録日時',
 `updated_by` varchar(15) NOT NULL COMMENT '更新者コード',
 `proxy_updated_by` varchar(15) DEFAULT NULL COMMENT '更新者コード（代行ログイン者コード）',
 `updated_at` datetime NOT NULL COMMENT '更新日時',
 constraint `t_approval_request_PKCPRIMARY`PRIMARY KEY (`request_id`)
) COMMENT='承認申請' ENGINE=InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci ;

-- CREATE INDEX `approval_idx` ON `t_approval_request`(`request_target_id`,`request_user_cd`);


