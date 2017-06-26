<?php
/**
 * Created by PhpStorm.
 * User: GGJRW
 * Date: 2017/6/7
 * Time: 16:15
 */

namespace org\freebook\model;

use org\freebook\dao\Dictcity AS DictcityDao;

class Dictcity
{
    public function getAll($where){
        $dict_city_dao = new DictcityDao();
        $result = $dict_city_dao->getAll($where);
        return $result;
    }
    public function getOne($request)
    {
        $dict_city_dao = new DictcityDao();
        return $result=$dict_city_dao->getOne($request);
    }
    public function city_tree($data){
        $city = new DictcityDao();
        if($data){
            foreach ($data as $key => $val) {
                $data[$key]['children'] = $city->where(['parent_id' => $val['id']])->select();
                if($data[$key]['children']){
                    $this->city_tree($data[$key]['children']);
                }
            }
        }
        return $data;
    }
    public function getArea($city_id){
        $city = new DictcityDao();
        $map = [
            'level_type'=>'3',
            'parent_id'=>$city_id
        ];
        return $city->field('id, name')->where($map)->select();
    }
}