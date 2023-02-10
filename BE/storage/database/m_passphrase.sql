DROP TABLE IF EXISTS `m_passphrase`;

CREATE TABLE `m_passphrase` (
   `user_cd` varchar(15) NOT NULL COMMENT 'ユーザーコード',
   `passphrase` varchar(100) NOT NULL COMMENT 'パスワードハッシュ',
   `require_change_flag` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '変更要求フラグ:フラグが立ってるの間は変更要求し続ける',
   `created_by` varchar(15) NOT NULL COMMENT '登録者コード',
   `proxy_created_by` varchar(15) DEFAULT NULL COMMENT '登録者コード（代行ログイン者）',
   `created_at` datetime NOT NULL COMMENT '登録日時',
   `updated_by` varchar(15) NOT NULL COMMENT '更新者コード',
   `proxy_updated_by` varchar(15) DEFAULT NULL COMMENT '更新者コード（代行ログイン者コード）',
   `updated_at` datetime NOT NULL COMMENT '更新日時',
   constraint `m_passphrase_PKC` PRIMARY KEY (`user_cd`)
 ) COMMENT='パスフレーズマスタ' ENGINE=InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci ;