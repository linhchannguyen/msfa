DROP TABLE IF EXISTS `m_facility_addition`;

CREATE TABLE `m_facility_addition` (
   `facility_cd` varchar(25) NOT NULL COMMENT '施設コード',
   `dpc_flag` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT 'DPC対象フラグ',
   `dpc_approval_date` date DEFAULT NULL COMMENT 'DPC指定日付',
   `dpc_annulment_date` date DEFAULT NULL COMMENT 'DPC取消日付',
   `dpc_prep_flag` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT 'DPC準備フラグ',
   `dpc_prep_year` varchar(4) DEFAULT NULL COMMENT 'DPC準備年',
   `dpc_prep_annulment_date` date DEFAULT NULL COMMENT 'DPC準備取消日付',
   `first_medical_subjects_cd` varchar(3) DEFAULT NULL COMMENT '第一標榜:独自項目',
   `institution_date` date DEFAULT NULL COMMENT '設立日:独自項目',
   `opener_name` varchar(40) DEFAULT NULL COMMENT '開設者:独自項目',
   `doctor_count` varchar(5) DEFAULT NULL COMMENT '医師数:独自項目',
   `outpatient_count` varchar(5) DEFAULT NULL COMMENT '外来患者数:独自項目',
   `non_consultation` varchar(30) DEFAULT NULL COMMENT '休診日:独自項目',
   `created_by` varchar(15) NOT NULL COMMENT '登録者コード',
   `proxy_created_by` varchar(15) DEFAULT NULL COMMENT '登録者コード（代行ログイン者）',
   `created_at` datetime NOT NULL COMMENT '登録日時',
   `updated_by` varchar(15) NOT NULL COMMENT '更新者コード',
   `proxy_updated_by` varchar(15) DEFAULT NULL COMMENT '更新者コード（代行ログイン者コード）',
   `updated_at` datetime NOT NULL COMMENT '更新日時',
   constraint `m_facility_addition_PKC` PRIMARY KEY (`facility_cd`)
 ) COMMENT='施設マスタ（追加ファイルアイテム）' ENGINE=InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci ;