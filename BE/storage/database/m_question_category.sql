DROP TABLE IF EXISTS `m_question_category`;

CREATE TABLE `m_question_category` (
  `qa_category_cd` varchar(3) NOT NULL COMMENT 'カテゴリコード',
  `qa_category_name` varchar(20) NOT NULL COMMENT 'カテゴリ名',
  `delete_flag` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '削除フラグ',
  `created_by` varchar(15) NOT NULL COMMENT '登録者コード',
  `proxy_created_by` varchar(15) DEFAULT NULL COMMENT '登録者コード（代行ログイン者）',
  `created_at` datetime NOT NULL COMMENT '登録日時',
  `updated_by` varchar(15) NOT NULL COMMENT '更新者コード',
  `proxy_updated_by` varchar(15) DEFAULT NULL COMMENT '更新者コード（代行ログイン者コード）',
  `updated_at` datetime NOT NULL COMMENT '更新日時',
  constraint `m_question_category_PKC` PRIMARY KEY (`qa_category_cd`)
) COMMENT='QAカテゴリマスタ:QAのカテゴリマスタ' ENGINE=InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci ;