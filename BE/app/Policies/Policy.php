<?php
namespace App\Policies;

class Policy 
{
    // FE Menu permission
    public $menuPermission = \App\Policies\Permissions\MenuPermission::class;

    // FE Route permission
    public $routePermission = \App\Policies\Permissions\RoutePermission::class;

    // Mock your permissions here
    public $screenPermissions = [
        \App\Policies\Permissions\HomePermission::class,
        \App\Policies\Permissions\MessageManagementPermission::class,
        \App\Policies\Permissions\UserManagementPermission::class,
    ];
}