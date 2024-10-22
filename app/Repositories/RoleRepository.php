<?php

namespace App\Repositories;

use App\Models\Role;

class RoleRepository
{
    protected Role $model;
    public function __construct(Role $model)
    {
        $this->model = $model;
    }

    /**
     * @param $roleId
     * @return string
     */
    public function getRoleName($roleId): string
    {
        $role = $this->model->newQuery()
            ->where('id', '=', $roleId)
            ->selectRaw('name')
            ->first();
        return $role ? $role->name : '';
    }
}
