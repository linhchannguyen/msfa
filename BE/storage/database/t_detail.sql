DROP TABLE IF EXISTS `t_detail`;

CREATE TABLE `t_detail` (
   `detail_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ディテールID',
   `call_id` bigint(20) unsigned NOT NULL COMMENT '面談ID',
   `detail_order` int(4) NOT NULL COMMENT 'ディテール順',
   `content_cd` varchar(3) NOT NULL COMMENT '面談内容コード',
   `content_name_tran` varchar(50) DEFAULT NULL COMMENT '面談内容（当時）',
   `product_cd` varchar(10) DEFAULT NULL COMMENT '品目コード',
   `product_name` varchar(30) DEFAULT NULL COMMENT '品目名（当時）',
   `note` text DEFAULT NULL COMMENT '特記事項（公開）',
   `remarks` varchar(300) DEFAULT NULL COMMENT '活動メモ',
   `phase_cd` varchar(4) DEFAULT NULL COMMENT 'フェーズコード',
   `phase` varchar(20) DEFAULT NULL COMMENT 'フェーズ（当時）',
   `reaction_cd` varchar(4) DEFAULT NULL COMMENT '反応コード',
   `reaction` varchar(20) DEFAULT NULL COMMENT '反応（当時）',
   `prescription_count` int(11) DEFAULT NULL COMMENT '症例数',
   `created_by` varchar(15) NOT NULL COMMENT '登録者コード',
   `proxy_created_by` varchar(15) DEFAULT NULL COMMENT '登録者コード（代行ログイン者）',
   `created_at` datetime NOT NULL COMMENT '登録日時',
   `updated_by` varchar(15) NOT NULL COMMENT '更新者コード',
   `proxy_updated_by` varchar(15) DEFAULT NULL COMMENT '更新者コード（代行ログイン者コード）',
   `updated_at` datetime NOT NULL COMMENT '更新日時',
   constraint `t_detail_PKC` PRIMARY KEY (`detail_id`)
 ) COMMENT='ディテール' ENGINE=InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci ;

create index `td_meet_id`
  on `t_detail`(`call_id`);
