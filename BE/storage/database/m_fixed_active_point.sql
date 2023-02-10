DROP TABLE IF EXISTS `m_fixed_active_point`;

CREATE TABLE `m_fixed_active_point` (
   `fixed_active_point_cd` varchar(3) NOT NULL COMMENT '固定ポイント区分',
   `active_point` int(11) NOT NULL COMMENT 'ポイント',
   `remarks` varchar(200) DEFAULT NULL COMMENT '区分詳細備考:何のポイントかわかる説明を記載',
   `created_by` varchar(15) NOT NULL COMMENT '登録者コード',
   `proxy_created_by` varchar(15) DEFAULT NULL COMMENT '登録者コード（代行ログイン者）',
   `created_at` datetime NOT NULL COMMENT '登録日時',
   `updated_by` varchar(15) NOT NULL COMMENT '更新者コード',
   `proxy_updated_by` varchar(15) DEFAULT NULL COMMENT '更新者コード（代行ログイン者コード）',
   `updated_at` datetime NOT NULL COMMENT '更新日時',
   constraint `m_fixed_active_point_PKC` PRIMARY KEY (`fixed_active_point_cd`)
 ) COMMENT='活用度固定ポイントマスタ:各機能で付与する活用度ポイントの固定ポイントを設定。' ENGINE=InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci ;