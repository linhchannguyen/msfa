DROP TABLE IF EXISTS `t_phase`;

CREATE TABLE `t_phase` (
   `facility_cd` varchar(25) NOT NULL COMMENT '施設コード',
   `person_cd` varchar(15) NOT NULL COMMENT '個人コード',
   `product_cd` varchar(10) NOT NULL COMMENT '品目コード',
   `phase_cd` varchar(4) NOT NULL COMMENT 'フェーズコード',
   `final_content_date` date NOT NULL COMMENT '最終面談日',
   `created_by` varchar(15) NOT NULL COMMENT '登録者コード',
   `proxy_created_by` varchar(15) DEFAULT NULL COMMENT '登録者コード（代行ログイン者）',
   `created_at` datetime NOT NULL COMMENT '登録日時',
   `updated_by` varchar(15) NOT NULL COMMENT '更新者コード',
   `proxy_updated_by` varchar(15) DEFAULT NULL COMMENT '更新者コード（代行ログイン者コード）',
   `updated_at` datetime NOT NULL COMMENT '更新日時',
   constraint `t_phase_PKC` PRIMARY KEY (`facility_cd`,`person_cd`,`product_cd`)
 ) COMMENT='フェーズ' ENGINE=InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci ;

create index `td_meet_id`
  on `t_phase`(`person_cd`);