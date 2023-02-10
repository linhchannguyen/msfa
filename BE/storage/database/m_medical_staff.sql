DROP TABLE IF EXISTS `m_medical_staff`;

CREATE TABLE `m_medical_staff` (
   `medical_staff_cd` varchar(15) NOT NULL COMMENT '職種コード:個人コードと衝突しないようにセットする',
   `medical_staff_name` varchar(30) NOT NULL COMMENT '職種名',
   `sort_order` tinyint(6) unsigned NOT NULL COMMENT '表示順',
   `created_by` varchar(15) NOT NULL COMMENT '登録者コード',
   `proxy_created_by` varchar(15) DEFAULT NULL COMMENT '登録者コード（代行ログイン者）',
   `created_at` datetime NOT NULL COMMENT '登録日時',
   `updated_by` varchar(15) NOT NULL COMMENT '更新者コード',
   `proxy_updated_by` varchar(15) DEFAULT NULL COMMENT '更新者コード（代行ログイン者コード）',
   `updated_at` datetime NOT NULL COMMENT '更新日時',
   constraint `m_medical_staff_PKC` PRIMARY KEY (`medical_staff_cd`)
 ) COMMENT='医療従事者区分マスタ' ENGINE=InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci ;