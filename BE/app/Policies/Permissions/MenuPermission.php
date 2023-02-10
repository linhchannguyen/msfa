<?php
namespace App\Policies\Permissions;

use App\Policies\PermissionInterface;

class MenuPermission implements PermissionInterface
{
    public $groupName = 'menu';
    public $memo = 'Menu permission';


    /**
     * Using * for all permissions
     * Leave blank for none
     */
    public function permissions(): array
    {
        return [
            'R01' => [
                '_1'
            ],
            'R10' => [
                '_1',
                '_2',
                '_2_1',
                '_2_2',
                '_2_3',
                '_3',
                '_3_1',
                '_3_2',
                '_4',
                '_5',
                '_6',
                '_6_1',
                '_6_2',
                '_7',
                '_7_1',
                '_7_2',
                '_8',
                '_8_1',
                '_8_2',
                '_9',
                '_9_1',
                '_9_2',
                '_9_3',
                '_10',
                '_10_1',
                '_10_2',
                '_11',
                '_11_1',
                '_11_2',
                '_11_3',
                '_11_4',
                '_12',
                '_13',
                '_13_1',
                '_13_2',
                '_13_3'
            ],
            'R20' => ['*'],
            'R31' => [
                '_1'
            ],
            'R30' => ['*'],
            'R40' => ['*'],
            'R50' => ['*'],
            'R60' => ['*'],
            'R70' => ['*'],
            'R80' => ['*'],
            'R90' => ['*'],
            'RDev' => ['*'],
        ];
    }


    /**
     * Using * for all elements
     * Leave blank for none 
     */
    public function elements(): array
    {
        return [
            '_1' => ['ホーム'],
            '_2' => ['面談管理'],
            '_2_1' => ['スケジュール入力'],
            '_2_2' => ['カレンダー'],
            '_2_3' => ['ストック管理'],
            '_3' => ['日報報告'],
            '_3_1' => ['日報一覧'],
            '_3_2' => ['未承認一覧'],
            '_4' => ['説明会管理'],
            '_5' => ['講演会管理'],
            '_6' => ['資材管理'],
            '_6_1' => ['資材一覧'],
            '_6_2' => ['カスタム資材'],
            '_7' => ['QA管理'],
            '_7_1' => ['QA一覧'],
            '_7_2' => ['投稿禁止ユーザ管理'],
            '_8' => ['ナレッジ管理'],
            '_8_1' => ['ナレッジ一覧'],
            '_8_2' => ['ナレッジ承認一覧'],
            '_9' => ['ターゲット管理'],
            '_9_1' => ['ターゲット施設一覧'],
            '_9_2' => ['ターゲット施設個人設定'],
            '_9_3' => ['ターゲット個人選定期間設定'],
            '_10' => ['顧客管理'],
            '_10_1' => ['施設検索'],
            '_10_2' => ['個人検索'],
            '_11' => ['マスタ管理'],
            '_11_1' => ['ユーザ管理'],
            '_11_2' => ['権限設定'],
            '_11_3' => ['承認者設定'],
            '_11_4' => ['パスワード再発行'],
            '_12' => ['メッセージ管理'],
            '_13' => ['パーソナル設定'],
            '_13_1' => ['セレクト施設設定'],
            '_13_2' => ['セレクト施設個人設定'],
            '_13_3' => ['面談内容設定'],
        ];
    }

    public function hiddenElements(): array
    {
        return [
            'hidden_on_pc' => [

            ],

            'hidden_on_tablet' => [

            ],

            'hidden_on_smartphone' => [

            ]
        ];
    }
}