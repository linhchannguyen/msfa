DROP TABLE IF EXISTS `t_briefing_attendee`;

CREATE TABLE `t_briefing_attendee` (
   `briefing_attendee_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '説明会出席者ID',
   `briefing_id` bigint(20) unsigned NOT NULL COMMENT '説明会ID',
   `person_cd` varchar(15) DEFAULT NULL COMMENT '個人コード',
   `facility_cd` varchar(25) NOT NULL COMMENT '施設コード',
   `person_name` varchar(50) NOT NULL COMMENT '個人名（当時）',
   `person_name_kana` varchar(50) DEFAULT NULL COMMENT '個人名ｶﾅ（当時）',
   `facility_short_name` varchar(50) DEFAULT NULL COMMENT '施設略名（当時）',
   `facility_name_kana` varchar(100) DEFAULT NULL COMMENT '施設略名ｶﾅ（当時）',
   `department_cd` varchar(4) DEFAULT NULL COMMENT '所属部科コード（当時）',
   `department_name` varchar(100) DEFAULT NULL COMMENT '所属部科名（当時）',
   `display_position_cd` varchar(3) DEFAULT NULL COMMENT '表示役職コード（当時）',
   `display_position_name` varchar(25) DEFAULT NULL COMMENT '表示役職名（当時）',
   `other_medical_staff_type` varchar(15) DEFAULT NULL COMMENT 'その他個人医療従事者区分',
   `other_person_flag` tinyint(1) unsigned NOT NULL COMMENT 'その他個人フラグ',
   `created_by` varchar(15) NOT NULL COMMENT '登録者コード',
   `proxy_created_by` varchar(15) DEFAULT NULL COMMENT '登録者コード（代行ログイン者）',
   `created_at` datetime NOT NULL COMMENT '登録日時',
   `updated_by` varchar(15) NOT NULL COMMENT '更新者コード',
   `proxy_updated_by` varchar(15) DEFAULT NULL COMMENT '更新者コード（代行ログイン者コード）',
   `updated_at` datetime NOT NULL COMMENT '更新日時',
   constraint `t_briefing_attendee_PKC` PRIMARY KEY (`briefing_attendee_id`)
 ) COMMENT='説明会出席者' ENGINE=InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci ;