<?php

namespace App\Http\Controllers\Admin\Role;

use App\Facades\Notice;
use App\Http\Requests\Admin\Role\RoleIndexRequest;
use App\Models\Role;
use App\Tools\Tree;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RoleController extends Controller
{
    /**
     * Author sam
     * DateTime 2019-06-10 11:42
     * Description:
     * @param RoleIndexRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(RoleIndexRequest $request)
    {
        $page = $request->get('page',null);
        $size = $request->get('size',null);
        $with = $request->get('with',null);
        $query = Role::query();
        if(!empty($with)){
            $query->with($with);
        }
        if(!empty($page)&&!empty($size)){
            $list = $query->paginate($size);
        }else{
            $list = $query->get();
        }
        return success($list);
    }

    /**
     * Author sam
     * DateTime 2019-09-26 14:35
     * Description:添加角色
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $params = $request->all();
        Role::query()->create($params);
        return success();
    }

    /**
     * Author sam
     * DateTime 2019-06-11 12:15
     * Description:角色详情
     * @param $role
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($role)
    {
        $role = Role::query()->with('privileges')->find($role);
        $role->rolePrivileges =  Tree::getTree($role->privileges->toArray());
        unset($role->privileges);
        return success($role);
    }

    /**
     * Author sam
     * DateTime 2019-09-26 14:16
     * Description:修改角色信息
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $params = $request->except('id');
        /**
         * @var $role Role
         */
        $role = Role::query()->find($id);
        $role->updateLogic($params);
        return success($params);
    }

    /**
     * Author sam
     * DateTime 2019-09-26 14:39
     * Description:删除角色
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        /**
         * @var $role Role
         */
        $role = Role::query()->with('admins')->find($id);
        if($role->admins->isNotEmpty()){
            return error('该角色下有账号关联');
        }
        Role::destroy($id);
        return success();
    }
    
    public function updatePri(Request $request,Role $role)
    {
        $renew = $request->get('renew');
        $role->privileges()->sync($renew);
        return success();
    }
}
