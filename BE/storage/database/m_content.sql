DROP TABLE IF EXISTS `m_content`;

CREATE TABLE `m_content` (
   `content_cd` varchar(3) NOT NULL COMMENT '面談内容コード:DTL10:製品紹介\r\nDTL20:情報伝達\r\nDTL30:情報収集\r\nDTL40:クレーム対応\r\nDTL50:説明会\r\nDTL60:研究会・講演会\r\nWS10:本部商談\r\nWS20:MS商談\r\nWS30:スポット\r\nWS40:勉強会\r\nELSE:その他\r\nNOCALL:面談できず',
   `content_name` varchar(50) NOT NULL COMMENT '面談内容',
   `available_start_date` date NOT NULL COMMENT '開始日付',
   `available_end_date` date NOT NULL COMMENT '終了日付',
   `sort_order` tinyint(6) unsigned DEFAULT NULL COMMENT '表示順',
   `created_by` varchar(15) NOT NULL COMMENT '登録者コード',
   `proxy_created_by` varchar(15) DEFAULT NULL COMMENT '登録者コード（代行ログイン者）',
   `created_at` datetime NOT NULL COMMENT '登録日時',
   `updated_by` varchar(15) NOT NULL COMMENT '更新者コード',
   `proxy_updated_by` varchar(15) DEFAULT NULL COMMENT '更新者コード（代行ログイン者コード）',
   `updated_at` datetime NOT NULL COMMENT '更新日時',
   constraint `m_content_PKC` PRIMARY KEY (`content_cd`)
 ) COMMENT='面談内容マスタ:内容マスタ' ENGINE=InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci ;