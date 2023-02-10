DROP TABLE IF EXISTS `m_facility`;

CREATE TABLE `m_facility` (
   `facility_cd` varchar(25) NOT NULL COMMENT '施設コード',
   `facility_category_type` varchar(3) NOT NULL COMMENT '施設分類',
   `ultmarc_facility_cd` varchar(25) DEFAULT NULL COMMENT 'アルトマーク施設コード',
   `facility_name` varchar(100) NOT NULL COMMENT '施設名',
   `facility_short_name` varchar(50) NOT NULL COMMENT '施設略名',
   `facility_name_kana` varchar(100) NOT NULL COMMENT '施設名ｶﾅ',
   `facility_short_name_kana` varchar(100) NOT NULL COMMENT '施設略名ｶﾅ',
   `post_number` varchar(10) DEFAULT NULL COMMENT '郵便番号',
   `prefecture_cd` varchar(2) NOT NULL COMMENT '都道府県コード',
   `municipality_cd` varchar(3) NOT NULL COMMENT '市区町村コード',
   `town_cd` varchar(3) DEFAULT NULL COMMENT '大字・通称コード',
   `small_section_cd` varchar(3) DEFAULT NULL COMMENT '字町目コード',
   `address` varchar(100) NOT NULL COMMENT '施設所在地',
   `address_kana` varchar(100) NOT NULL COMMENT '施設所在地ｶﾅ',
   `address_number` varchar(13) DEFAULT NULL COMMENT '住所表示番号',
   `phone` varchar(15) DEFAULT NULL COMMENT '電話番号',
   `director_cd` varchar(15) DEFAULT NULL COMMENT '代表者コード',
   `director_name` varchar(50) DEFAULT NULL COMMENT '代表者名',
   `director_name_kana` varchar(20) DEFAULT NULL COMMENT '代表者名ｶﾅ',
   `management_body_cd` varchar(3) NOT NULL COMMENT '経営体コード',
   `facility_type` varchar(2) NOT NULL COMMENT '施設区分:DCF、DNFﾌｧｲﾙ.施設区分／DSFﾌｧｲﾙ.種別コード／ECFﾌｧｲﾙ.分類種別コード',
   `infirmary_type` varchar(1) NOT NULL COMMENT '病院種別',
   `relation_university_cd` varchar(25) DEFAULT NULL COMMENT '関連大学親コード',
   `general_bed_count` varchar(5) DEFAULT NULL COMMENT '許可病床数（一般）',
   `cure_bed_count` varchar(5) DEFAULT NULL COMMENT '許可病床数（療養）',
   `mental_bed_count` varchar(5) DEFAULT NULL COMMENT '許可病床数（精神）',
   `tuberculous_bed_count` varchar(5) DEFAULT NULL COMMENT '許可病床数（結核）',
   `infection_bed_count` varchar(5) DEFAULT NULL COMMENT '許可病床数（感染）',
   `other_bed_count` varchar(5) DEFAULT NULL COMMENT '許可病床数（その他）',
   `total_bed_count` varchar(5) DEFAULT NULL COMMENT '許可病床数（合計）',
   `update_bed_count_date` date DEFAULT NULL COMMENT '許可病床数更新日',
   `break_flag` tinyint(1) unsigned DEFAULT NULL COMMENT '休院フラグ',
   `break_start_ym` varchar(6) DEFAULT NULL COMMENT '休院開始年月:yyyymmで指定',
   `delete_reason_type` varchar(1) DEFAULT NULL COMMENT '削除理由コード:変数定義マスタ「CUSTOMER_DELETE_REASON」',
   `ultmarc_delete_flag` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT 'アルトマーク削除フラグ:1:削除（入力には使用しない）',
   `facility_close_flag` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '病棟閉鎖フラグ',
   `created_by` varchar(15) NOT NULL COMMENT '登録者コード',
   `proxy_created_by` varchar(15) DEFAULT NULL COMMENT '登録者コード（代行ログイン者）',
   `created_at` datetime NOT NULL COMMENT '登録日時',
   `updated_by` varchar(15) NOT NULL COMMENT '更新者コード',
   `proxy_updated_by` varchar(15) DEFAULT NULL COMMENT '更新者コード（代行ログイン者コード）',
   `updated_at` datetime NOT NULL COMMENT '更新日時',
   constraint `m_facility_PKC` PRIMARY KEY (`facility_cd`)
 ) COMMENT='施設マスタ' ENGINE=InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci ;

-- CREATE INDEX `mc_customer_short_name` on `m_facility`(`facility_short_name`);

-- CREATE INDEX `mc_customer_short_name_kana` on `m_facility`(`facility_short_name_kana`);

-- CREATE INDEX `mc_prefecture_municipality` on `m_facility`(`prefecture_cd`, `municipality_cd`);

-- CREATE INDEX `mc_channel_type` on `m_facility`(`facility_category_type`);