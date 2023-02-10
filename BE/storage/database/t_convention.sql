DROP TABLE IF EXISTS `t_convention`;

CREATE TABLE `t_convention` (
   `convention_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '講演会ID',
   `schedule_id` bigint(20) unsigned NOT NULL COMMENT 'スケジュールID',
   `status_type` varchar(2) NOT NULL COMMENT 'ステータス区分',
   `hold_type` varchar(2) DEFAULT NULL COMMENT '開催区分',
   `parent_series_flag` tinyint(1) unsigned NOT NULL COMMENT '親シリーズフラグ',
   `series_convention_id` bigint(20) unsigned DEFAULT NULL COMMENT 'シリーズ講演会ID',
   `convention_type` varchar(2) DEFAULT NULL COMMENT '講演会区分',
   `hold_method` varchar(2) DEFAULT NULL COMMENT '開催方法',
   `convention_name` varchar(100) NOT NULL COMMENT '講演会名',
   `place` varchar(255) DEFAULT NULL COMMENT '会場',
   `hold_purpose` text DEFAULT NULL COMMENT '開催目的',
   `remarks` text DEFAULT NULL COMMENT '備考',
   `hold_form` varchar(2) DEFAULT NULL COMMENT '開催形態',
   `cohost_partner_name` varchar(40) DEFAULT NULL COMMENT '共催相手名',
   `cost_share_type` varchar(2) DEFAULT NULL COMMENT '開催費分担区分',
   `cost_share_content` text DEFAULT NULL COMMENT '開催費分担内容',
   `expense_num` varchar(20) DEFAULT NULL COMMENT '経費番号',
   `disposal_technique` varchar(2) DEFAULT NULL COMMENT '弁当処分方法',
   `reason` text DEFAULT NULL COMMENT '差異理由および今後の対策',
   `note` text DEFAULT NULL COMMENT '特記事項',
   `plan_user_cd` varchar(15) NOT NULL COMMENT 'ユーザコード（企画者）',
   `plan_user_name` varchar(30) NOT NULL COMMENT 'ユーザ名（企画者当時）',
   `plan_org_cd` varchar(20) NOT NULL COMMENT '組織コード（企画者当時）',
   `plan_org_label` varchar(30) NOT NULL COMMENT '組織ラベル（企画者当時）',
   `remand_flag` tinyint(1) unsigned NOT NULL COMMENT '差戻しフラグ',
   `created_by` varchar(15) NOT NULL COMMENT '登録者コード',
   `proxy_created_by` varchar(15) DEFAULT NULL COMMENT '登録者コード（代行ログイン者）',
   `created_at` datetime NOT NULL COMMENT '登録日時',
   `updated_by` varchar(15) NOT NULL COMMENT '更新者コード',
   `proxy_updated_by` varchar(15) DEFAULT NULL COMMENT '更新者コード（代行ログイン者コード）',
   `updated_at` datetime NOT NULL COMMENT '更新日時',
   constraint `t_convention_PKC` PRIMARY KEY (`convention_id`)
 ) COMMENT='講演会' ENGINE=InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci ;