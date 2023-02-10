DROP TABLE IF EXISTS `t_briefing_attendee_count`;

CREATE TABLE `t_briefing_attendee_count` (
  `briefing_id` bigint(20) unsigned NOT NULL COMMENT '説明会ID',
  `occupation_type` varchar(10) NOT NULL COMMENT '職種区分',
  `plan_headcount` int(4) DEFAULT NULL COMMENT '計画人数',
  `result_headcount` int(4) DEFAULT NULL COMMENT '結果人数',
  `created_by` varchar(15) NOT NULL COMMENT '登録者コード',
  `proxy_created_by` varchar(15) DEFAULT NULL COMMENT '登録者コード（代行ログイン者）',
  `created_at` datetime NOT NULL COMMENT '登録日時',
  `updated_by` varchar(15) NOT NULL COMMENT '更新者コード',
  `proxy_updated_by` varchar(15) DEFAULT NULL COMMENT '更新者コード（代行ログイン者コード）',
  `updated_at` datetime NOT NULL COMMENT '更新日時',
  constraint `t_briefing_attendee_count_PKC` PRIMARY KEY (`briefing_id`,`occupation_type`)
) COMMENT='説明会出席者数' ENGINE=InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci ;