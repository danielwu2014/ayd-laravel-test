<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DevService
{
    public function executeQuery(Request $request)
    {
        $sql = $request->input('sql');
        $results = [];
        $error = '';
        $perPage = 2; // 每页显示的记录数

        // 验证 SQL 是否以 SELECT 开头
        $sql = rtrim($sql, ";");
        if (empty($sql)) {
            $error = "SQL statement cannot be empty";
        } elseif (stripos(trim($sql), 'select') !== 0) {
            $error = "Only SELECT statements are allowed.";
        } else {
            try {
                // 使用 DB::raw 和 paginate 实现分页
                $page = $request->input('page', 1); // 获取当前页码
                $offset = ($page - 1) * $perPage; // 计算偏移量

                // 获取记录总数
                $countQuery = DB::select("SELECT COUNT(*) as total FROM ($sql) as count_table");
                $total = $countQuery[0]->total;

                // 使用 LIMIT 和 OFFSET 实现分页
                $paginatedSql = $sql . " LIMIT $perPage OFFSET $offset";
                $results = DB::select($paginatedSql);

                // 创建分页对象
                $results = new \Illuminate\Pagination\LengthAwarePaginator($results, $total, $perPage, $page, [
                    'path' => $request->url(),
                    'query' => $request->query(),
                ]);
            } catch (\Exception $e) {
                // 捕捉 SQL 错误并返回
                $error = $e->getMessage();
            }
        }

        return view('dev', [
            'results' => $results,
            'error' => $error,
            'sql' => $sql,
        ]);
    }
}
