DROP TABLE IF EXISTS `t_call`;

CREATE TABLE `t_call` (
   `call_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '面談ID',
   `visit_id` bigint(20) unsigned NOT NULL COMMENT '訪問ID',
   `person_cd` varchar(15) NOT NULL COMMENT '個人コード',
   `plan_flag` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '計画フラグ',
   `act_flag` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '実施フラグ',
   `person_name` varchar(50) NOT NULL COMMENT '個人名（当時）',
   `department_cd` varchar(4) DEFAULT NULL COMMENT '所属部科コード（当時）',
   `department_name` varchar(100) DEFAULT NULL COMMENT '所属部科名（当時）',
   `display_position_name` varchar(10) DEFAULT NULL COMMENT '表示役職名（当時）',
   `off_label_flag` tinyint(1) unsigned NOT NULL COMMENT 'オフラベル有フラグ:1：あり\r\n0：なし',
   `off_label_concent_flag` tinyint(1) unsigned NOT NULL COMMENT 'オフラベル承諾フラグ:1：はい、0：いいえ',
   `off_label_call_time` int(11) DEFAULT NULL COMMENT 'オフラベル面談時間:オフラベルの項目\r\n何分間オフラベルの会話をしたのかを管理する',
   `related_product` varchar(50) DEFAULT NULL COMMENT '自社関連製品:オフラベルの項目',
   `question` varchar(300) DEFAULT NULL COMMENT '要求質問内容:オフラベルの項目',
   `answer` varchar(300) DEFAULT NULL COMMENT '回答内容:オフラベルの項目',
   `re_question` varchar(300) DEFAULT NULL COMMENT '回答に対する質問:オフラベルの項目',
   `literature` varchar(100) DEFAULT NULL COMMENT '文献名:オフラベルの項目',
   `created_by` varchar(15) NOT NULL COMMENT '登録者コード',
   `proxy_created_by` varchar(15) DEFAULT NULL COMMENT '登録者コード（代行ログイン者）',
   `created_at` datetime NOT NULL COMMENT '登録日時',
   `updated_by` varchar(15) NOT NULL COMMENT '更新者コード',
   `proxy_updated_by` varchar(15) DEFAULT NULL COMMENT '更新者コード（代行ログイン者コード）',
   `updated_at` datetime NOT NULL COMMENT '更新日時',
   constraint `t_call_PKC` PRIMARY KEY (`call_id`)
 ) COMMENT='面談' ENGINE=InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci ;
 

create index `tm_visit_person`
  on `t_call`(`visit_id`,`person_cd`);

create index `tm_person_visit`
  on `t_call`(`person_cd`,`visit_id`);
