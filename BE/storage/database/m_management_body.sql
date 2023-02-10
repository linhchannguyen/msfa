DROP TABLE IF EXISTS `m_management_body`;

CREATE TABLE `m_management_body` (
   `management_body_cd` varchar(3) NOT NULL COMMENT '経営体コード',
   `management_body_name` varchar(50) NOT NULL COMMENT '経営体名',
   `created_by` varchar(15) NOT NULL COMMENT '登録者コード',
   `proxy_created_by` varchar(15) DEFAULT NULL COMMENT '登録者コード（代行ログイン者）',
   `created_at` datetime NOT NULL COMMENT '登録日時',
   `updated_by` varchar(15) NOT NULL COMMENT '更新者コード',
   `proxy_updated_by` varchar(15) DEFAULT NULL COMMENT '更新者コード（代行ログイン者コード）',
   `updated_at` datetime NOT NULL COMMENT '更新日時',
   constraint `m_management_body_PKC` PRIMARY KEY (`management_body_cd`)
 ) COMMENT='経営体マスタ:アルトマークのマスタ' ENGINE=InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci ;