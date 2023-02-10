DROP TABLE IF EXISTS `m_phase`;

CREATE TABLE `m_phase` (
  `phase_cd` varchar(4) NOT NULL COMMENT 'フェーズコード',
  `phase_name` varchar(20) NOT NULL COMMENT 'フェーズ',
  `available_start_date` date NOT NULL COMMENT '開始日付',
  `available_end_date` date NOT NULL COMMENT '終了日付',
  `sort_order` tinyint(6) unsigned DEFAULT NULL COMMENT '表示順',
  `created_by` varchar(15) NOT NULL COMMENT '登録者コード',
  `proxy_created_by` varchar(15) DEFAULT NULL COMMENT '登録者コード（代行ログイン者）',
  `created_at` datetime NOT NULL COMMENT '登録日時',
  `updated_by` varchar(15) NOT NULL COMMENT '更新者コード',
  `proxy_updated_by` varchar(15) DEFAULT NULL COMMENT '更新者コード（代行ログイン者コード）',
  `updated_at` datetime NOT NULL COMMENT '更新日時',
  constraint `m_phase_PKC` PRIMARY KEY (`phase_cd`)
) COMMENT='フェーズマスタ' ENGINE=InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci ;