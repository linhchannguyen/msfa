DROP TABLE IF EXISTS `m_org`;

CREATE TABLE `m_org` (
   `org_cd` varchar(20) NOT NULL COMMENT '組織コード',
   `org_name` varchar(50) DEFAULT NULL COMMENT '組織名',
   `org_short_name` varchar(20) DEFAULT NULL COMMENT '組織略名',
   `org_label` varchar(50) DEFAULT NULL COMMENT '組織ラベル:組織略名を階層分つなげたものを設定',
   `layer_num` tinyint(1) unsigned DEFAULT NULL COMMENT '組織階層番号:１～５階層',
   `layer_1` varchar(20) DEFAULT NULL COMMENT '組織階層1',
   `layer_2` varchar(20) DEFAULT NULL COMMENT '組織階層2',
   `layer_3` varchar(20) DEFAULT NULL COMMENT '組織階層3',
   `layer_4` varchar(20) DEFAULT NULL COMMENT '組織階層4',
   `layer_5` varchar(20) DEFAULT NULL COMMENT '組織階層5',
   `sort_order` tinyint(6) unsigned DEFAULT NULL COMMENT '表示順',
   `created_by` varchar(15) NOT NULL COMMENT '登録者コード',
   `proxy_created_by` varchar(15) DEFAULT NULL COMMENT '登録者コード（代行ログイン者）',
   `created_at` datetime NOT NULL COMMENT '登録日時',
   `updated_by` varchar(15) NOT NULL COMMENT '更新者コード',
   `proxy_updated_by` varchar(15) DEFAULT NULL COMMENT '更新者コード（代行ログイン者コード）',
   `updated_at` datetime NOT NULL COMMENT '更新日時',
   constraint `m_org_PKC` PRIMARY KEY (`org_cd`)
 ) COMMENT='組織マスタ' ENGINE=InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci ;