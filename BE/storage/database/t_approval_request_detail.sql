DROP TABLE IF EXISTS `t_approval_request_detail`;

CREATE TABLE `t_approval_request_detail` (
   `request_id` bigint(20) unsigned NOT NULL COMMENT '申請ID',
   `layer_num` tinyint(1) NOT NULL COMMENT '承認階層:1,2,3,4,...',
   `approval_user_cd` varchar(15) NOT NULL COMMENT 'ユーザコード（承認者）',
   `status_type` varchar(3) DEFAULT NULL COMMENT 'ステータス区分:0:承認待ち | 1:承認済 | 2:差戻',
   `approval_datetime` datetime DEFAULT NULL COMMENT '承認日時',
   `comment` varchar(200) DEFAULT NULL COMMENT 'コメント',
   `created_by` varchar(15) NOT NULL COMMENT '登録者コード',
   `proxy_created_by` varchar(15) DEFAULT NULL COMMENT '登録者コード（代行ログイン者）',
   `created_at` datetime NOT NULL COMMENT '登録日時',
   `updated_by` varchar(15) NOT NULL COMMENT '更新者コード',
   `proxy_updated_by` varchar(15) DEFAULT NULL COMMENT '更新者コード（代行ログイン者コード）',
   `updated_at` datetime NOT NULL COMMENT '更新日時',
   constraint `t_approval_request_detail_PKC`  PRIMARY KEY (`request_id`,`layer_num`,`approval_user_cd`)
 ) COMMENT='承認申請明細' ENGINE=InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;