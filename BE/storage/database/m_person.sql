DROP TABLE IF EXISTS `m_person`;

CREATE TABLE `m_person` (
  `person_cd` varchar(15) NOT NULL COMMENT '個人コード',
  `ultmarc_person_cd` varchar(15) DEFAULT NULL COMMENT 'アルトマーク個人コード',
  `record_type` varchar(2) DEFAULT NULL COMMENT 'レコード区分:01:DCF医師ファイル、02:PCF院内薬剤師ファイル、05:DNF歯科医師ファイル',
  `person_name` varchar(50) NOT NULL COMMENT '個人名',
  `person_name_kana` varchar(50) NOT NULL COMMENT '個人名ｶﾅ',
  `gender` varchar(1) DEFAULT NULL COMMENT '性別',
  `birth_date` varchar(7) DEFAULT NULL COMMENT '生年月日:W(元号)+YY+MM+DD',
  `home_prefecture_cd` varchar(7) DEFAULT NULL COMMENT '出身都道府県コード',
  `medical_association_cd` varchar(2) DEFAULT NULL COMMENT '医師会コード:都道府県コードが設定される',
  `graduation_year` varchar(3) DEFAULT NULL COMMENT '卒業年:W(元号)+YY',
  `graduation_university_cd` varchar(4) DEFAULT NULL COMMENT '出身校コード:出身大学コード',
  `academic_department_cd` varchar(3) DEFAULT NULL COMMENT '学部識別コード:1:医学部、2:薬学部、3:歯学部',
  `acceptance_year` varchar(3) DEFAULT NULL COMMENT '登録年:W(元号)+YY',
  `work_type` varchar(1) DEFAULT NULL COMMENT '開勤区分:1:開業医、2:勤務医、9:不明',
  `practice_year` varchar(3) DEFAULT NULL COMMENT '開業年',
  `delete_reason_type` varchar(1) DEFAULT NULL COMMENT '削除理由',
  `ultmarc_delete_flag` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT 'アルトマーク削除フラグ:1:削除（入力には使用しない）',
  `created_by` varchar(15) NOT NULL COMMENT '登録者コード',
  `proxy_created_by` varchar(15) DEFAULT NULL COMMENT '登録者コード（代行ログイン者）',
  `created_at` datetime NOT NULL COMMENT '登録日時',
  `updated_by` varchar(15) NOT NULL COMMENT '更新者コード',
  `proxy_updated_by` varchar(15) DEFAULT NULL COMMENT '更新者コード（代行ログイン者コード）',
  `updated_at` datetime NOT NULL COMMENT '更新日時',
  constraint `m_person_PKC` PRIMARY KEY (`person_cd`)
) COMMENT='個人マスタ' ENGINE=InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

create index `INDX_ULT_PERSON_CD`
  on `m_person`(`ultmarc_person_cd`);