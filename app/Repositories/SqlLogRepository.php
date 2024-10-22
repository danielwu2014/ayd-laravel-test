<?php

namespace App\Repositories;

use App\Models\SqlLog;

class SqlLogRepository
{
    protected SqlLog $model;

    public function __construct(SqlLog $model)
    {
        $this->model = $model;
    }

    /**
     * @param $data
     * @return bool
     */
    public function addLog($data): bool
    {
        return $this->model->newQuery()->insert($data);
    }
}
