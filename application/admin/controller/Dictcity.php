<?php
/**
 * Created by PhpStorm.
 * User: liujinli
 * Date: 17/6/7
 * Time: 下午16:46
 */

namespace app\admin\controller;

use org\freebook\model\Dictcity as DictcityModel;
use think\Controller;

class Dictcity extends Controller
{

    //城市管理开始
    public function city()
    {
        return $this->fetch();
    }
    public function ajax_del_city(){
        if(request()->isAjax()){
            $city = new DataDictionary();
            $id = input('param.id');
            $result = $city->where(['parent_id' => $id])->find();
            if($result){
                return json(['code' => -1, 'data' =>'', 'msg' => '当前操作项下有子元素！']);
            }
            $flag = $city->where(['id'=>$id])->delete();
            if($flag){
                return json(['code' => 1, 'data' =>'', 'msg' => '删除成功']);
            }else{
                return json(['code' => -1, 'data' =>'', 'msg' => '删除失败']);
            }
        }
    }

    public function ajax_city_list(){
        if(request()->isAjax()) {
            $city = new DictcityModel();
            $where=['parent_id'=>420000];
            $data = $city->getAll($where);
            $result['treelists'] = $city->city_tree($data);
            return json($result);
        }
    }

    protected function findChild($arr,$children = '',$parentId){
        static $tree = array();
        if($parentId == 0){
            $children = '';
        }else{
            $children = '|--&nbsp;'.$children;
        }
        foreach ($arr as $key=>$val){
            $result[$key]['child'] = $children;
            $result[$key]['name'] = $val['name'];
            $result[$key]['id'] = $val['id'];
            $tree[] = $result[$key];
            if ($val['children']){
                $this -> findChild($val['children'],$children,$val['parent_id']);
            }
        }
        return $tree;
    }

}
