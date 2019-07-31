<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Repository\Admin\AdminUserRepository;
use Illuminate\Database\QueryException;
use Illuminate\Support\Str;
use App\Http\Requests\Admin\AdminUserManageRequest;
use App\Repository\Admin\AdminUserManageRepository;
use Symfony\Component\HttpFoundation\Request;

class UserManageController extends Controller
{
    protected $formNames = [
      'aid', 'username', 'pubdate', 'typeid', 'enable', 'pwd', 'realname', 'logintime', 'loginip'
    ];

    public function __construct()
    {
        parent::__construct();

        $this->breadcrumb[] = ['title' => '管理员管理', 'url' => route('admin::userManage.index')];
    }


    public function index()
    {
        $this->breadcrumb[] = ['title' => '管理员列表', 'url' => route('admin::userManage.list')];
        return view('admin.userManage.index', ['breadcrumb' => $this->breadcrumb]);
    }

    public function list(Request $request)
    {
        $perPage = (int) $request->get('limit', 50);
        $condition = $request->only($this->formNames);
        $data = AdminUserManageRepository::list($perPage, $condition);
        return $data;
    }

    public function add()
    {
        $this->breadcrumb[] = [
          'title' => '新增管理员',
          'url'   => ''
        ];
        return view('admin.userManage.add');
    }


    /**
     * @param $id
     */
    public function edit($id)
    {
        $this->breadcrumb[] = [
            'title' => '修改管理员',
            'url'   => ''
        ];
        $user = AdminUserManageRepository::find($id);
        return view('admin.userManage.add', [
            'id' => $id, 'user' => $user, 'breadcrumb' => $this->breadcrumb
        ]);
    }


    /**
     * 新增用户
     */
    public function create(AdminUserManageRequest $request)
    {
        try {
            $user = AdminUserManageRepository::add($request->only($this->formNames));
            AdminUserManageRepository::setDefaultPermission($user);
            return [
                'code' => 0,
                'msg' => '新增成功',
                'redirect' => true
            ];
        } catch (QueryException $e) {
            return [
                'code' => 1,
                'msg' => '新增失败：' . $e->getMessage().(Str::contains($e->getMessage(), 'Duplicate entry') ? '当前用户已存在' : '其它错误'),
                'redirect' => false
            ];
        }

    }


    /**
     * 更新用户信息
     * @param AdminUserManageRequest $request
     * @return array
     */
    public function update(AdminUserManageRequest $request)
    {
        try {
            $aid = $request->get('id');
            $postData = $request->only($this->formNames);
            $res = AdminUserManageRepository::update($aid, $postData);
            return [
              'code' => 0,
              'msg'  => '修改成功',
              'redirect' => true
            ];
        } catch (QueryException $e) {
            return [
                'code' => 1,
                'msg'  => '修改失败'. $e->getMessage(),
                'redirect' => false
            ];
        }
    }


    /**
     *
     * @param Request $request
     * @return array
     */
    public function delete(Request $request)
    {
        $ids = $request->get('ids');
        $res = AdminUserManageRepository::delete($ids);
        return [
          'code' => 0,
          'msg'  => '删除成功',
          'redirect' => true
        ];
    }

}