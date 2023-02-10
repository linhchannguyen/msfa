<?php
namespace App\Policies\Permissions;

use App\Policies\PermissionInterface;

class HomePermission implements PermissionInterface
{
    public $groupName = 'Z02_S01_Home';
    public $memo = 'Home screen permission';


    /**
     * CREATE = C
     * READ SELF = R
     * READ ALL = RA
     * UPDATE SELF = U
     * UPDATE ALL = UA
     * DELETE SELF = D
     * DELETE_ALL = DA
     * 
     * Using * for all permissions
     * Leave blank for none
     */
    public function permissions(): array
    {
        return [
            'R01' => ['RA'],
            'R10' => ['R'],
            'R20' => ['R'],
            'R31' => ['R'],
            'R30' => ['R'],
            'R40' => ['R'],
            'R50' => ['R'],
            'R60' => ['R'],
            'R70' => ['R'],
            'R80' => ['R'],
            'R90' => ['R'],
            'RDev' => [],
        ];
    }


    /**
     * CREATE = C
     * READ SELF = R
     * READ ALL = RA
     * UPDATE SELF = U
     * UPDATE ALL = UA
     * DELETE SELF = D
     * DELETE_ALL = DA
     * 
     * Using * for all elements
     * Leave blank for none 
     */
    public function elements(): array
    {
        return [
            'C' => [],
            'R' => ['*'],
            'RA' => ['*'],
            'U' => [],
            'UA' => [],
            'D' => [],
            'DA' => []
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