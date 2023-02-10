<?php

namespace App\Policies;

interface PermissionInterface {
    public function permissions(): array;
    public function elements(): array;
    public function hiddenElements(): array;
}