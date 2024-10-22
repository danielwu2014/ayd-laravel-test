<?php

namespace App\Http\Controllers;

use App\Services\DevService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DevController extends Controller
{
    protected DevService $devService;

    public function __construct(DevService $devService)
    {
        $this->devService = $devService;
    }

    /**
     * @return Application|Factory|View|\Illuminate\Foundation\Application
     */
    public function index(): \Illuminate\Foundation\Application|View|Factory|Application
    {
        return view('dev'); // 渲染dev.blade.php视图
    }

    /**
     * @param Request $request
     * @return Application|Factory|View|\Illuminate\Foundation\Application
     */
    public function executeQuery(Request $request): \Illuminate\Foundation\Application|View|Factory|Application
    {
        return $this->devService->executeQuery($request);
    }

    /**
     * @param Request $request
     * @return RedirectResponse|null
     */
    public function exportExcel(Request $request): ?RedirectResponse
    {
        return $this->devService->exportExcel($request);
    }

    /**
     * @param Request $request
     * @return RedirectResponse|null
     */
    public function exportJson(Request $request): ?RedirectResponse
    {
        return $this->devService->exportJson($request);
    }
}
