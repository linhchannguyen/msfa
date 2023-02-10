DROP TABLE IF EXISTS `t_stock`;

CREATE TABLE `t_stock` (
   `stock_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ストックID',
   `facility_cd` varchar(25) DEFAULT NULL COMMENT '施設コード',
   `person_cd` varchar(15) DEFAULT NULL COMMENT '個人コード',
   `content_cd` varchar(3) DEFAULT NULL COMMENT '面談内容コード',
   `status_type` varchar(3) DEFAULT NULL COMMENT 'ステータス区分:0:計画済み　1:未計画',
   `stock_type` varchar(3) DEFAULT NULL COMMENT 'ストック元区分',
   `action_id` bigint(20) unsigned DEFAULT NULL COMMENT 'ストック元ID:講演会と説明会のIDを設定',
   `stock_date` date DEFAULT NULL COMMENT 'ストック日付',
   `created_by` varchar(15) NOT NULL COMMENT '登録者コード',
   `proxy_created_by` varchar(15) DEFAULT NULL COMMENT '登録者コード（代行ログイン者）',
   `created_at` datetime NOT NULL COMMENT '登録日時',
   `updated_by` varchar(15) NOT NULL COMMENT '更新者コード',
   `proxy_updated_by` varchar(15) DEFAULT NULL COMMENT '更新者コード（代行ログイン者コード）',
   `updated_at` datetime NOT NULL COMMENT '更新日時',
   constraint `t_stock_PKC` PRIMARY KEY (`stock_id`)
 ) COMMENT='ストック' ENGINE=InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci ;

create index `tes_user`
  on `t_stock`(`person_cd`);