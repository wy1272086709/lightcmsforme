<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class IntervalTimeController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->breadcrumb[] = [
            'title' => '参数配置',
            'url' => route('admin::areaModule.index')
        ];
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->breadcrumb[] = [
            'title' => '间隔时间管理',
            'url'   => ''
        ];
        $time = Redis::get('mn_interval_time');
        //
        return view('admin.timeInterval', [
            'breadcrumb'   => $this->breadcrumb,
            'intervalTime' => $time
        ]);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $time = Redis::get('mn_interval_time');
            $val = $request->get('mn_interval_time');
            // 如果对应的传递过来的值小于0 或者 redis中对应点的键不存在
            if ((int)$val <= 0 || !$time) {
                $val = config()->get('mn_interval_time');
            }
            Redis::set('mn_interval_time', $val);
            return [
                'code' => 0,
                'msg' => '设置成功'
            ];
        } catch (\Exception $ex) {
            return [
                'code' => 1,
                'msg'  => '设置失败!失败原因:'. $ex->getMessage()
            ];
        }
    }

}
