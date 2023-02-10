<?php

namespace App\Repositories\RolePolicy;

use Illuminate\Support\Facades\DB;
use App\Repositories\BaseRepository;
use App\Repositories\RolePolicy\RolePolicyRepositoryInterface;

class RolePolicyRepository extends BaseRepository implements RolePolicyRepositoryInterface
{
    protected $useAutoMeta = true;

    public function getRoleList($userCd)
    {
        $sql = "
            SELECT
                role
            FROM
                m_user_role
            WHERE
                user_cd = :user_cd;
        ";

        return $this->_find($sql, ["user_cd" => $userCd]);
    }

    public function getScreen($roles)
    {
        $sql = "
        SELECT
            c_screen.screen_cd,
            c_screen.component,
            c_screen.screen_name,
            c_screen.menu_cd,
            c_screen.url,
            c_screen.pc_visible_flag,
            c_screen.tablet_visible_flag,
            c_screen.smartphone_visible_flag
        FROM
            c_screen,
            c_screen_policy
        WHERE
            c_screen.screen_cd = c_screen_policy.screen_cd and
            c_screen_policy.role IN ".  $this->_buildCommaString($roles, true);
    $sql .= "
        GROUP BY c_screen.screen_cd
        ORDER BY CAST(c_screen.sort_order as SIGNED)
    ";
    return $this->_all($sql);
    }
    public function getMenu($roles)
    {
        $sql = "
        SELECT
           c_menu.menu_cd,
           c_menu.menu_name,
           c_menu.sort_order,
           (SELECT CONCAT('[', GROUP_CONCAT(JSON_OBJECT(
            'screen_cd',c_screen.screen_cd,
            'component',c_screen.component,
            'screen_name',c_screen.screen_name,
            'menu_cd',c_screen.menu_cd,
            'sort_order',c_screen.sort_order,
            'url',c_screen.url,
            'pc_visible_flag',c_screen.pc_visible_flag,
            'tablet_visible_flag',c_screen.tablet_visible_flag,
            'smartphone_visible_flag',c_screen.smartphone_visible_flag
            )),']')
            FROM c_screen
            WHERE c_menu.menu_cd = c_screen.menu_cd
            ORDER BY CAST(c_screen.sort_order as SIGNED)
    ) AS screen
        FROM
            c_screen,
            c_screen_policy,
            c_menu
        WHERE
            c_screen.screen_cd = c_screen_policy.screen_cd and
            c_menu.menu_cd = c_screen.menu_cd and
            c_screen_policy.role IN ".  $this->_buildCommaString($roles, true);
    $sql .= "
        GROUP BY c_menu.menu_cd
        ORDER BY CAST(c_menu.sort_order as SIGNED)
    ";
    return $this->_all($sql);
    }
}
