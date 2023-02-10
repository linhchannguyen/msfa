DROP TABLE IF EXISTS `m_facility_delete_reason_type`;

CREATE TABLE `m_facility_delete_reason_type` (
  `delete_reason_type` varchar(1) NOT NULL COMMENT '削除予定理由区分',
  `delete_reason_type_label` varchar(100) NOT NULL COMMENT '削除予定理由名',
  `created_by` varchar(15) NOT NULL COMMENT '登録者コード',
  `proxy_created_by` varchar(15) DEFAULT NULL COMMENT '登録者コード（代行ログイン者）',
  `created_at` datetime NOT NULL COMMENT '登録日時',
  `updated_by` varchar(15) NOT NULL COMMENT '更新者コード',
  `proxy_updated_by` varchar(15) DEFAULT NULL COMMENT '更新者コード（代行ログイン者コード）',
  `updated_at` datetime NOT NULL COMMENT '更新日時',
  constraint `m_facility_delete_reason_type_PKC` PRIMARY KEY (`delete_reason_type`)
) COMMENT='施設削除予定理由区分マスタ:アルトマークのマスタ' ENGINE=InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci ;