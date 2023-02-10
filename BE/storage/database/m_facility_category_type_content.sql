DROP TABLE IF EXISTS `m_facility_category_type_content`;

CREATE TABLE `m_facility_category_type_content` (
   `facility_category_type` varchar(2) NOT NULL COMMENT '施設分類',
   `content_cd` varchar(3) NOT NULL COMMENT '面談内容コード',
   `created_by` varchar(15) NOT NULL COMMENT '登録者コード',
   `proxy_created_by` varchar(15) DEFAULT NULL COMMENT '登録者コード（代行ログイン者）',
   `created_at` datetime NOT NULL COMMENT '登録日時',
   `updated_by` varchar(15) NOT NULL COMMENT '更新者コード',
   `proxy_updated_by` varchar(15) DEFAULT NULL COMMENT '更新者コード（代行ログイン者コード）',
   `updated_at` datetime NOT NULL COMMENT '更新日時',
   constraint `m_facility_category_type_content_PKC` PRIMARY KEY (`facility_category_type`,`content_cd`)
 ) COMMENT='施設分類別面談マップ' ENGINE=InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci ;
