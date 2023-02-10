DROP TABLE IF EXISTS `m_calendar`;

CREATE TABLE `m_calendar` (
   `calendar_date` date NOT NULL COMMENT '日付',
   `calendar_year` year(4) NOT NULL COMMENT '年',
   `calendar_month` tinyint(2) NOT NULL COMMENT '月:0埋めするとSFA側で正常に処理できないので0埋めしないこと',
   `calendar_day` tinyint(2) NOT NULL COMMENT '日:0埋めするとSFA側で正常に処理できないので0埋めしないこと',
   `holiday_flag` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '休日フラグ:1:非営業日（休日）\r\n0:営業日',
   `business_day_count` tinyint(2) NOT NULL COMMENT '営業日カウント',
   `created_by` varchar(15) NOT NULL COMMENT '登録者コード',
   `proxy_created_by` varchar(15) DEFAULT NULL COMMENT '登録者コード（代行ログイン者）',
   `created_at` datetime NOT NULL COMMENT '登録日時',
   `updated_by` varchar(15) NOT NULL COMMENT '更新者コード',
   `proxy_updated_by` varchar(15) DEFAULT NULL COMMENT '更新者コード（代行ログイン者コード）',
   `updated_at` datetime NOT NULL COMMENT '更新日時',
   constraint `m_calendar_PKC` PRIMARY KEY (`calendar_date`)
 ) COMMENT='カレンダマスタ' ENGINE=InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci ;