DROP TABLE IF EXISTS `m_facility_category`;

CREATE TABLE `m_facility_category` (
   `facility_category_type` varchar(3) NOT NULL COMMENT '施設分類',
   `facility_category_name` varchar(100) DEFAULT NULL COMMENT '施設分類名',
   `created_by` varchar(15) NOT NULL COMMENT '登録者コード',
   `proxy_created_by` varchar(15) DEFAULT NULL COMMENT '登録者コード（代行ログイン者）',
   `created_at` datetime NOT NULL COMMENT '登録日時',
   `updated_by` varchar(15) NOT NULL COMMENT '更新者コード',
   `proxy_updated_by` varchar(15) DEFAULT NULL COMMENT '更新者コード（代行ログイン者コード）',
   `updated_at` datetime NOT NULL COMMENT '更新日時',
   constraint `m_facility_category_PKC` PRIMARY KEY (`facility_category_type`)
 ) COMMENT='施設分類マスタ:施設の分類分けの一種。MTSで設定する。（大学病院、大病院（200床以上）、病院（100～200床）、診療所・開業医（100床未満）、卸など、活動内容が異なるもの）' ENGINE=InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci ;