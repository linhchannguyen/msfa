DROP TABLE IF EXISTS `m_relation_facility`;

CREATE TABLE `m_relation_facility` (
   `facility_cd` varchar(25) NOT NULL COMMENT '親施設コード',
   `relation_facility_cd` varchar(25) NOT NULL COMMENT '子施設コード',
   `relation_type` varchar(3) NOT NULL COMMENT '関連施設区分:変数定義マスタ参照（関連施設区分）',
   `created_by` varchar(15) NOT NULL COMMENT '登録者コード',
   `proxy_created_by` varchar(15) DEFAULT NULL COMMENT '登録者コード（代行ログイン者）',
   `created_at` datetime NOT NULL COMMENT '登録日時',
   `updated_by` varchar(15) NOT NULL COMMENT '更新者コード',
   `proxy_updated_by` varchar(15) DEFAULT NULL COMMENT '更新者コード（代行ログイン者コード）',
   `updated_at` datetime NOT NULL COMMENT '更新日時',
   constraint `m_relation_facility_PKC` PRIMARY KEY (`facility_cd`,`relation_facility_cd`,`relation_type`)
 ) COMMENT='関連施設マスタ' ENGINE=InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci ;