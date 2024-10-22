<?php

namespace App\Services;

use App\Repositories\SqlLogRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

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

        $this->recordExecuteLog($sql, $error);

        return view('dev', [
            'results' => $results,
            'error' => $error,
            'sql' => $sql,
        ]);
    }

    public function exportExcel(Request $request)
    {
        try {
            $sql = $request->input('sql_query');
            // 去除 SQL 语句末尾的分号
            $sql = rtrim($sql, ";");
            // 验证 SQL 是否以 SELECT 开头
            if (stripos(trim($sql), 'select') !== 0) {
                return redirect()->back()->with('error', 'Only SELECT statements are allowed.');
            }
            // 执行 SQL 查询
            $results = DB::select($sql);
            // 记录日志
            $this->recordExecuteLog($sql);
        } catch (\Exception $e) {
            $this->recordExecuteLog($e->getMessage());
            return redirect()->back()->with('error', $e->getMessage());
        }

        // 创建新的 Spreadsheet 对象
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        if (!empty($results)) {
            // 添加标题行
            $columns = array_keys((array)$results[0]); // 获取第一行的列名
            foreach ($columns as $index => $column) {
                $sheet->setCellValue(chr(65 + $index) . '1', ucfirst($column)); // A1, B1, C1...
            }

            // 添加数据
            $row = 2; // 从第二行开始插入数据
            foreach ($results as $result) {
                foreach ($columns as $index => $column) {
                    $sheet->setCellValue(chr(65 + $index) . $row, $result->$column); // 动态获取列名
                }
                $row++;
            }
        }

        // 设置文件名
        $filename = 'query_results.xlsx';

        // 输出文件
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit();
    }

    public function exportJson(Request $request)
    {
        try {
            $sql = $request->input('sql_query');

            // 去除 SQL 语句末尾的分号
            $sql = rtrim($sql, ";");

            // 验证 SQL 是否以 SELECT 开头
            if (stripos(trim($sql), 'select') !== 0) {
                throw new \Exception('Only SELECT statements are allowed.');
            }

            // 执行 SQL 查询
            $results = DB::select($sql);
            // 记录日志
            $this->recordExecuteLog($sql);
        } catch (\Exception $e) {
            $this->recordExecuteLog($e->getMessage());
            return redirect()->back()->with('error', $e->getMessage());
        }

        // 将结果转换为数组
        $resultsArray = json_decode(json_encode($results), true); // 转换 stdClass 对象为数组

        // 设置文件名
        $filename = 'query_results.json';

        // 输出文件头部
        header('Content-Type: application/json');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        // 输出 JSON 数据
        echo json_encode($resultsArray, JSON_PRETTY_PRINT);
        exit();
    }

    /**
     * @param $sql
     * @param string $error
     * @return void
     */
    public function recordExecuteLog($sql, string $error = ''): void
    {
        $datetime = date('Y-m-d H:i:s');
        $data     = [
            'user_id'       => auth()->user()->getAuthIdentifier(),
            'executed_at'   => $datetime,
            'sql_statement' => $sql,
            'error'         => $error,
            'created_at'    => $datetime,
            'updated_at'    => $datetime,
        ];

        /** @var SqlLogRepository $sqlLogRepository */
        $sqlLogRepository = app(SqlLogRepository::class);
        $sqlLogRepository->addLog($data);
    }
}
