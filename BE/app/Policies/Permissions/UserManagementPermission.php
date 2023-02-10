<?php
namespace App\Policies\Permissions;

use App\Policies\PermissionInterface;

class UserManagementPermission implements PermissionInterface
{
    public $groupName = 'I01_S01_UserManagement';
    public $memo = 'User Management';


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
            'R01' => [],
            'R10' => [],
            'R20' => [],
            'R31' => [],
            'R30' => [],
            'R40' => [],
            'R50' => [],
            'R60' => [],
            'R70' => [],
            'R80' => ['C', 'U', 'RA', 'D'],
            'R90' => ['C', 'U', 'RA', 'D'],
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
            'C' => [
                'add_new_button' => 'Add new button'
            ],
            'R' => [],
            'RA' => ['*'],
            'U' => [
                'save_button' => 'Save button'
            ],
            'UA' => [
                'save_button' => 'Save button'
            ],
            'D' => [
                'delete_button' => 'Delete button'
            ],
            'DA' => [
                'delete_button' => 'Delete button'
            ],
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