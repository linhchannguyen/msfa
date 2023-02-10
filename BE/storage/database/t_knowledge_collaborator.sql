DROP TABLE IF EXISTS `t_knowledge_collaborator`;

CREATE TABLE `t_knowledge_collaborator` (
   `knowledge_id` bigint(20) unsigned NOT NULL COMMENT 'ナレッジID',
   `user_cd` varchar(15) NOT NULL COMMENT 'ユーザコード',
   `created_by` varchar(15) NOT NULL COMMENT '登録者コード',
   `proxy_created_by` varchar(15) DEFAULT NULL COMMENT '登録者コード（代行ログイン者）',
   `created_at` datetime NOT NULL COMMENT '登録日時',
   `updated_by` varchar(15) NOT NULL COMMENT '更新者コード',
   `proxy_updated_by` varchar(15) DEFAULT NULL COMMENT '更新者コード（代行ログイン者コード）',
   `updated_at` datetime NOT NULL COMMENT '更新日時',
   constraint `t_knowledge_collaborator_PKC` PRIMARY KEY (`knowledge_id`,`user_cd`)
 ) COMMENT='ナレッジ共同作成者:ナレッジの共同作成者' ENGINE=InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci ;