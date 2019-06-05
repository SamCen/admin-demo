<?php

namespace App\Http\Controllers\Admin\User;

use App\Contract\RedisKey;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Menu;
use App\Models\Privilege;
use App\Tools\Tree;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class AdminController extends Controller
{
    /**
     * Author sam
     * DateTime 2019-06-03 17:03
     * Description:获取用户信息
     * @return \Illuminate\Http\JsonResponse
     */
    public function getInfo()
    {
        /**
         * @var $user Admin
         */
        $user = Auth::user();
        $menus = $this->getMenus($user);
        $user->menus = $menus;
        return success($user);
    }

    /**
     * Author sam
     * DateTime 2019-06-04 11:50
     * Description:获取菜单
     * @param $user
     * @return array|\Illuminate\Support\Collection
     */
    protected function getMenus($user)
    {
        $privileges = $this->getPrivileges($user);
        $redisKey = RedisKey::ADMIN_MENUS;
        if ($menus = Redis::hget($redisKey, $user->id)) {
            return $this->buildMenus(json_decode($menus,true));
        } else {
            $menus = [];
            foreach ($privileges as $privilege) {
                if ($privilege->menu_id) {
                    $menus[] = Menu::where('id',$privilege->menu_id)->first();
                }
            }
            $menus = collect($menus);
            Redis::hset($redisKey, $user->id, json_encode($menus));
            Redis::expire($redisKey, 86400);
            return $this->buildMenus($menus);
        }

    }

    /**
     * Author sam
     * DateTime 2019-06-04 11:49
     * Description:获取用户的权限
     * @param $user
     * @return array|mixed
     */
    protected function getPrivileges($user)
    {
        $redisKey = RedisKey::ADMIN_PRIVILEGES;
        if ($privileges = Redis::hget($redisKey, $user->id)) {
            return json_decode($privileges);
        } else {
            $privileges = [];
            if ($user->id == 1) {
                foreach (Privilege::get() as $privilege) {
                    $privileges[$privilege->code] = $privilege;
                }
            } else {
                $roles = $user->roles;
                foreach ($roles as $role) {
                    foreach ($role->privileges as $privilege) {
                        $privileges[$privilege->code] = $privilege;
                    }
                }
            }
            Redis::hset($redisKey, $user->id, json_encode($privileges));
            Redis::expire($redisKey, 86400);
            return $privileges;
        }
    }

    /**
     * Author sam
     * DateTime 2019-06-04 11:49
     * Description:生成菜单树
     * @param $data
     * @return array|\Illuminate\Support\Collection
     */
    protected function buildMenus($data)
    {
        $menus = collect($data)->sortBy('id');
        $menus->values()->all();
        $menus = Tree::getTree($menus);
        return $menus;
    }

    public function index()
    {
        
    }
}