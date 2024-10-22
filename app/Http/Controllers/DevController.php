<?php

namespace App\Http\Controllers;

use App\Services\DevService;
use Illuminate\Http\Request;

class DevController extends Controller
{
    protected DevService $devService;

    public function __construct(DevService $devService)
    {
        $this->devService = $devService;
    }

    public function index()
    {
        return view('dev'); // 渲染dev.blade.php视图
    }

    public function executeQuery(Request $request)
    {
        return $this->devService->executeQuery($request);
    }
}
