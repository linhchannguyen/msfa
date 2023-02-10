DROP TABLE IF EXISTS `m_consideration_type`;

CREATE TABLE `m_consideration_type` (
   `consideration_type` varchar(3) NOT NULL COMMENT '注意事項種別コード',
   `consideration_type_name` varchar(20) NOT NULL COMMENT '注意事項種別名',
   `use_facility_flag` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '施設使用フラグ',
   `use_person_flag` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '個人使用フラグ',
   `created_by` varchar(15) NOT NULL COMMENT '登録者コード',
   `proxy_created_by` varchar(15) DEFAULT NULL COMMENT '登録者コード（代行ログイン者）',
   `created_at` datetime NOT NULL COMMENT '登録日時',
   `updated_by` varchar(15) NOT NULL COMMENT '更新者コード',
   `proxy_updated_by` varchar(15) DEFAULT NULL COMMENT '更新者コード（代行ログイン者コード）',
   `updated_at` datetime NOT NULL COMMENT '更新日時',
   constraint `m_consideration_type_PKC` PRIMARY KEY (`consideration_type`)
 ) COMMENT='注意事項種別マスタ' ENGINE=InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci ;