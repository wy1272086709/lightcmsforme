<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Http\Services\AreaXmlService;
use Illuminate\Routing\Route;

class AreaXmlController extends Controller
{
    public function index()
    {
        $this->breadcrumb[] = [
            'title' => '查看配置', 'url' => route('admin::areaXml.index')
        ];
        return view('admin.areaXml.index', [
            'breadcrumb' => $this->breadcrumb
        ]);
    }


    public function list()
    {
        $taskId = (int)request()->get('taskId', 0);
        $page   = request()->get('page', 1);
        $perPage = request()->get('limit', 50);
        $service = new AreaXmlService;
        $total = $service->countAreaXml($taskId);
        $taskId?$condition = [
            'taskId' => $taskId
        ]: $condition = [ ];
        $res   = $service->getAreaXml($perPage, $page, $condition);
        return [
            'data'  => $res,
            'count' => $total,
            'msg'   => '',
            'code'  => 0
        ];
    }
}