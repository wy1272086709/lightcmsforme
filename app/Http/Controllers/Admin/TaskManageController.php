<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;

class TaskManageController extends Controller
{
    public function index()
    {
        $this->breadcrumb[] = [
            'title' => 'PC任务首页',
            'url'   => ''
        ];
        return view('admin.pcTask.index', [
            'breadcrumb' => $this->breadcrumb
        ]);
    }
}