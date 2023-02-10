DROP TABLE IF EXISTS `t_document`;

CREATE TABLE `t_document` (
   `document_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '資材ID',
   `document_type` varchar(2) NOT NULL COMMENT '資材区分:原本資材 or カスタム資材',
   `document_name` varchar(50) NOT NULL COMMENT '資材名',
   `start_date` date NOT NULL COMMENT '適用期間開始日',
   `end_date` date NOT NULL COMMENT '適用期間終了日',
   `document_contents` varchar(300) DEFAULT NULL COMMENT '概要',
   `disease` varchar(50) DEFAULT NULL COMMENT '対象疾患',
   `medical_subjects_group_cd` varchar(5) DEFAULT NULL COMMENT '診療科目分類コード',
   `safety_information_flag` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '安全性情報フラグ',
   `off_label_information_flag` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '適用外情報フラグ',
   `create_user_cd` varchar(15) NOT NULL COMMENT 'ユーザコード（作成者）',
   `last_update_datetime` datetime NOT NULL COMMENT '最終更新日時',
   `file_type` varchar(3) NOT NULL COMMENT 'ファイルタイプ',
   `created_by` varchar(15) NOT NULL COMMENT '登録者コード',
   `proxy_created_by` varchar(15) DEFAULT NULL COMMENT '登録者コード（代行ログイン者）',
   `created_at` datetime NOT NULL COMMENT '登録日時',
   `updated_by` varchar(15) NOT NULL COMMENT '更新者コード',
   `proxy_updated_by` varchar(15) DEFAULT NULL COMMENT '更新者コード（代行ログイン者コード）',
   `updated_at` datetime NOT NULL COMMENT '更新日時',
   constraint `t_document_PKC` PRIMARY KEY (`document_id`)
 ) COMMENT='資材' ENGINE=InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci ;