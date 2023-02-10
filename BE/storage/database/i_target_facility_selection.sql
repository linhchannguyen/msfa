DROP TABLE IF EXISTS `i_target_facility_selection`;

CREATE TABLE `i_target_facility_selection` (
   `facility_cd` varchar(25) NOT NULL COMMENT '施設コード',
   `target_product_cd` varchar(10) NOT NULL COMMENT 'ターゲット品目コード',
   `sub_target` varchar(5) DEFAULT NULL COMMENT 'サブターゲット',
   `created_by` varchar(15) NOT NULL COMMENT '登録者コード',
   `proxy_created_by` varchar(15) DEFAULT NULL COMMENT '登録者コード（代行ログイン者）',
   `created_at` datetime NOT NULL COMMENT '登録日時',
   `updated_by` varchar(15) NOT NULL COMMENT '更新者コード',
   `proxy_updated_by` varchar(15) DEFAULT NULL COMMENT '更新者コード（代行ログイン者コード）',
   `updated_at` datetime NOT NULL COMMENT '更新日時',
   constraint `i_target_facility_selection_PKC` PRIMARY KEY (`facility_cd`,`target_product_cd`)
 ) COMMENT='ターゲット施設(選定)' ENGINE=InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci ;