DROP TABLE IF EXISTS `c_system_parameter`;

CREATE TABLE `c_system_parameter` (
   `parameter_name` varchar(20) NOT NULL COMMENT '設定名',
   `parameter_key` varchar(20) NOT NULL COMMENT '設定キー',
   `parameter_value` varchar(100) DEFAULT NULL COMMENT '設定値',
   `remarks` varchar(200) DEFAULT NULL COMMENT '備考',
   `created_by` varchar(15) NOT NULL COMMENT '登録者コード',
   `proxy_created_by` varchar(15) DEFAULT NULL COMMENT '登録者コード（代行ログイン者）',
   `created_at` datetime NOT NULL COMMENT '登録日時',
   `updated_by` varchar(15) NOT NULL COMMENT '更新者コード',
   `proxy_updated_by` varchar(15) DEFAULT NULL COMMENT '更新者コード（代行ログイン者コード）',
   `updated_at` datetime NOT NULL COMMENT '更新日時',
   constraint `c_system_parameter_PKC` PRIMARY KEY (`parameter_name`,`parameter_key`)
 ) COMMENT='システム変数:システムの振る舞いを決めたり、実装されているものと密接に関連する設定値を保持する。\r\nユーザーによる変更不可。' ENGINE=InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci ;