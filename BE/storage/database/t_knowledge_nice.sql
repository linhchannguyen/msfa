DROP TABLE IF EXISTS `t_knowledge_nice`;

CREATE TABLE `t_knowledge_nice` (
   `nice_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'いいねID',
   `knowledge_id` bigint(20) unsigned NOT NULL COMMENT 'ナレッジID',
   `comment` text DEFAULT NULL COMMENT 'コメント',
   `nice_user_cd` varchar(15) NOT NULL COMMENT 'ユーザコード（いいね登録者）',
   `last_update_datetime` datetime NOT NULL COMMENT '最終更新日時',
   `delete_flag` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '評価コメント削除フラグ:1:削除　評価コメントの削除。いいね自体は削除扱いにはしない',
   `created_by` varchar(15) NOT NULL COMMENT '登録者コード',
   `proxy_created_by` varchar(15) DEFAULT NULL COMMENT '登録者コード（代行ログイン者）',
   `created_at` datetime NOT NULL COMMENT '登録日時',
   `updated_by` varchar(15) NOT NULL COMMENT '更新者コード',
   `proxy_updated_by` varchar(15) DEFAULT NULL COMMENT '更新者コード（代行ログイン者コード）',
   `updated_at` datetime NOT NULL COMMENT '更新日時',
   constraint `t_knowledge_nice_PKC` PRIMARY KEY (`nice_id`)
 ) COMMENT='ナレッジいいね:ナレッジへのいいね・評価コメントデータ' ENGINE=InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci ;


 alter table t_knowledge_nice add unique `knowledge_id,nice_user_cd` (knowledge_id,nice_user_cd) ;
