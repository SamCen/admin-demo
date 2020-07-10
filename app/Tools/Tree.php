<?php
/**
 * Author sam
 * DateTime 2019-06-04 11:21
 * Description:
 */

namespace App\Tools;


use App\Exceptions\GeneralException;
use Illuminate\Support\Collection;

class Tree
{
    /**
     * Author sam
     * DateTime 2019-09-26 17:53
     * Description:
     * @param $data
     * @param string $pid
     * @param string $priKey
     * @param string $parentKey
     * @param string $childKey
     * @return array
     */
    public static function getTree($data,$pid = 'base',$priKey = 'code',$parentKey = 'parent_code',$childKey = 'children')
    {
        $tree = [];
        foreach ($data as $k => $v){
            if($v[$parentKey] == $pid){
                $v[$childKey] = self::getTree($data,$v[$priKey],$priKey,$parentKey,$childKey)?:null;
                $tree[] = $v;
            }
        }
        return $tree;
    }
}
