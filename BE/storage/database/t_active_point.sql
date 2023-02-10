DROP TABLE IF EXISTS `t_active_point`;

CREATE TABLE `t_active_point` (
   `point_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ポイントID',
   `point_target_type` varchar(3) NOT NULL COMMENT 'ポイント対象区分:ポイントが登録された機能区分。資材/ナレッジ/QA',
   `point_target_id` bigint(20) unsigned NOT NULL COMMENT 'ポイント対象ID:ナレッジID/いいねID/質問ID/回答ID/資材ID',
   `active_point` int(11) NOT NULL COMMENT 'ポイント',
   `message` varchar(100) NOT NULL COMMENT '獲得メッセージ:獲得履歴に表示するメッセージ',
   `receive_user_cd` varchar(15) NOT NULL COMMENT 'ユーザコード（受領者）',
   `created_by` varchar(15) NOT NULL COMMENT '登録者コード',
   `proxy_created_by` varchar(15) DEFAULT NULL COMMENT '登録者コード（代行ログイン者）',
   `created_at` datetime NOT NULL COMMENT '登録日時',
   `updated_by` varchar(15) NOT NULL COMMENT '更新者コード',
   `proxy_updated_by` varchar(15) DEFAULT NULL COMMENT '更新者コード（代行ログイン者コード）',
   `updated_at` datetime NOT NULL COMMENT '更新日時',
   constraint `t_active_point_PKC` PRIMARY KEY (`point_id`)
 ) COMMENT='活用度ポイント:活用度ポイントの明細データ' ENGINE=InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci ;