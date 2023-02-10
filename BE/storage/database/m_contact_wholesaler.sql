DROP TABLE IF EXISTS `m_contact_wholesaler`;

CREATE TABLE `m_contact_wholesaler` (
  `wholesaler_cd` varchar(25) NOT NULL COMMENT '卸コード:卸コード',
  `layer_number` tinyint(1) NOT NULL COMMENT '卸階層番号:１～５階層',
  `wholesaler_name` varchar(50) NOT NULL COMMENT '卸名',
  `wholesaler_name_kana` varchar(100) DEFAULT NULL COMMENT '卸名ｶﾅ',
  `wholesaler_short_name` varchar(50) NOT NULL COMMENT '卸略名',
  `wholesaler_short_name_kana` varchar(100) DEFAULT NULL COMMENT '卸略名ｶﾅ',
  `layer_1` varchar(25) DEFAULT NULL COMMENT '卸階層1',
  `layer_2` varchar(25) DEFAULT NULL COMMENT '卸階層2',
  `layer_3` varchar(25) DEFAULT NULL COMMENT '卸階層3',
  `layer_4` varchar(25) DEFAULT NULL COMMENT '卸階層4',
  `layer_5` varchar(25) DEFAULT NULL COMMENT '卸階層5',
  `wholesaler_type` varchar(2) NOT NULL COMMENT '卸区分:10：卸、20：販社',
  `post_number` varchar(10) DEFAULT NULL COMMENT '郵便番号',
  `prefecture_cd` varchar(2) NOT NULL COMMENT '都道府県コード',
  `municipality_cd` varchar(3) NOT NULL COMMENT '市区町村コード',
  `address` varchar(100) NOT NULL COMMENT '施設所在地',
  `address_kana` varchar(100) NOT NULL COMMENT '施設所在地ｶﾅ',
  `phone` varchar(15) DEFAULT NULL COMMENT '電話番号',
  `delete_flag` varchar(1) NOT NULL DEFAULT '0' COMMENT '削除フラグ',
  `sort_order` int(11) DEFAULT NULL COMMENT '表示順',
  `import_facility_flag` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '施設取込フラグ',
  `created_by` varchar(15) NOT NULL COMMENT '登録者コード',
  `proxy_created_by` varchar(15) DEFAULT NULL COMMENT '登録者コード（代行ログイン者）',
  `created_at` datetime NOT NULL COMMENT '登録日時',
  `updated_by` varchar(15) NOT NULL COMMENT '更新者コード',
  `proxy_updated_by` varchar(15) DEFAULT NULL COMMENT '更新者コード（代行ログイン者コード）',
  `updated_at` datetime NOT NULL COMMENT '更新日時',
  constraint `m_contact_wholesaler_PKC` PRIMARY KEY (`wholesaler_cd`,`layer_number`)
) COMMENT='自社卸マスタ' ENGINE=InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

create index `mcw_layer_1`
  on `m_contact_wholesaler`(`layer_1`);