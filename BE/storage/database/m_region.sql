DROP TABLE IF EXISTS `m_region`;

CREATE TABLE `m_region` (
   `region_cd` varchar(3) NOT NULL COMMENT '地方コード',
   `region_name` varchar(10) NOT NULL COMMENT '地方名',
   `created_by` varchar(15) NOT NULL COMMENT '登録者コード',
   `proxy_created_by` varchar(15) DEFAULT NULL COMMENT '登録者コード（代行ログイン者）',
   `created_at` datetime NOT NULL COMMENT '登録日時',
   `updated_by` varchar(15) NOT NULL COMMENT '更新者コード',
   `proxy_updated_by` varchar(15) DEFAULT NULL COMMENT '更新者コード（代行ログイン者コード）',
   `updated_at` datetime NOT NULL COMMENT '更新日時',
   constraint `m_region_PKC` PRIMARY KEY (`region_cd`)
 ) COMMENT='地方マスタ:北海道、東北、関東、中部、近畿、中国、四国、九州・沖縄' ENGINE=InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci ;