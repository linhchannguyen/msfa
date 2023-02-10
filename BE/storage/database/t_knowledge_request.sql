DROP TABLE IF EXISTS `t_knowledge_request`;

CREATE TABLE `t_knowledge_request` (
   `demand_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '要望要求事項ID',
   `knowledge_id` bigint(20) unsigned NOT NULL COMMENT 'ナレッジID',
   `demand_note` text NOT NULL COMMENT '要望要求事項',
   `create_datetime` datetime DEFAULT NULL COMMENT '作成日時',
   `created_by` varchar(15) NOT NULL COMMENT '登録者コード',
   `proxy_created_by` varchar(15) DEFAULT NULL COMMENT '登録者コード（代行ログイン者）',
   `created_at` datetime NOT NULL COMMENT '登録日時',
   `updated_by` varchar(15) NOT NULL COMMENT '更新者コード',
   `proxy_updated_by` varchar(15) DEFAULT NULL COMMENT '更新者コード（代行ログイン者コード）',
   `updated_at` datetime NOT NULL COMMENT '更新日時',
   constraint `t_knowledge_request_PKC` PRIMARY KEY (`demand_id`)
 ) COMMENT='ナレッジ要望・要求事項:ナレッジへの要望・要求事項データ' ENGINE=InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci ;