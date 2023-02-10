<?php
/**
 * define variable for web
 * @author huynh
 */

return [
    // define where output file transaction part 9．操作ログ過去データ削除, https://docs.google.com/spreadsheets/d/1Azc8FcpilY40imcsxKp4urhwRAwsb1_h/edit#gid=1043991743
    'convert_param_to_base64' => env('APP_BASE64', true),
    'out_put_file_transaction' => env('OUT_PUT_FILE_TRANSACTION_PART_9', 'transaction_pass_data'),
    'MSFA_SYSTEM_OPERATION_DATE' => 'システム運用日付',
    'email_log_level' => env('EMAIL_LOG_LEVEL', "error"),
    'excute_log_sql' => env('EXCUTE_LOG_SQL', true),
    'allow_url_get_file' => env('ALLOW_URL_GET_FILE', 'api-msfa.ominext.dev,localhost:8000,localhost:8080'),
    'log_send_mail_flag' => env('LOG_SEND_EMAIL_FLAG', true),
    'log_default_mail_to' => env('LOG_DEFAULT_EMAIL'),
    'url_folder_export_log' => env('URL_FOLDER_EXPORT_LOG'),
    'env_name' => env('ENV_NAME', 'production'),
];
