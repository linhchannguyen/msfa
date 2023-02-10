DROP TABLE IF EXISTS `m_facility_person`;

CREATE TABLE `m_facility_person` (
   `facility_cd` varchar(25) NOT NULL COMMENT '施設コード',
   `person_cd` varchar(15) NOT NULL COMMENT '個人コード',
   `hospital_position_cd` varchar(3) DEFAULT NULL COMMENT '院内役職コード:役職マスタ参照',
   `academic_position_cd` varchar(3) DEFAULT NULL COMMENT '大学職位コード:役職マスタ参照',
   `display_position_cd` varchar(3) DEFAULT NULL COMMENT '表示役職コード:夜間バッチで生成。大学職位＞院内役職',
   `department_cd` varchar(4) DEFAULT NULL COMMENT '所属部科コード',
   `move_flag` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '異動フラグ:0：4週間以内に異動無し、1：4週間以内に異動有り\r\n ※組織内異動を除く',
   `created_by` varchar(15) NOT NULL COMMENT '登録者コード',
   `proxy_created_by` varchar(15) DEFAULT NULL COMMENT '登録者コード（代行ログイン者）',
   `created_at` datetime NOT NULL COMMENT '登録日時',
   `updated_by` varchar(15) NOT NULL COMMENT '更新者コード',
   `proxy_updated_by` varchar(15) DEFAULT NULL COMMENT '更新者コード（代行ログイン者コード）',
   `updated_at` datetime NOT NULL COMMENT '更新日時',
   constraint `m_facility_person_PKC` PRIMARY KEY (`facility_cd`,`person_cd`)
 ) COMMENT='施設個人マスタ:アルトマーク：DCF医師_勤務先ファイル' ENGINE=InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci ;
 
create index `mcp_person_facility`
  on `m_facility_person`(`person_cd`,`facility_cd`);