DROP TABLE IF EXISTS `t_knowledge_timeline`;

CREATE TABLE `t_knowledge_timeline` (
   `knowledge_id` bigint(20) unsigned NOT NULL COMMENT 'ナレッジID',
   `timeline_id` bigint(20) unsigned NOT NULL COMMENT 'タイムラインID',
   `channel_type` varchar(2) NOT NULL COMMENT '活動区分',
   `action_id` bigint(20) unsigned NOT NULL COMMENT '活動ID',
   `comment` text DEFAULT NULL COMMENT '詳細コメント',
   `created_by` varchar(15) NOT NULL COMMENT '登録者コード',
   `proxy_created_by` varchar(15) DEFAULT NULL COMMENT '登録者コード（代行ログイン者）',
   `created_at` datetime NOT NULL COMMENT '登録日時',
   `updated_by` varchar(15) NOT NULL COMMENT '更新者コード',
   `proxy_updated_by` varchar(15) DEFAULT NULL COMMENT '更新者コード（代行ログイン者コード）',
   `updated_at` datetime NOT NULL COMMENT '更新日時',
   constraint `t_knowledge_timeline_PKC` PRIMARY KEY (`knowledge_id`,`timeline_id`)
 ) COMMENT='ナレッジタイムライン:ナレッジとタイムラインの紐付けデータ' ENGINE=InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci ;