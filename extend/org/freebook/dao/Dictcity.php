<?php
/**
 * Created by PhpStorm.
 * User: GGJRW
 * Date: 2017/6/7
 * Time: 16:15
 */

namespace org\freebook\dao;

use think\Model;

class Dictcity extends Model
{
    protected $table = 'dict_city';

    public function getAll($where)
    {
        return $this->where($where)->select();
    }
    public function getOne($id)
    {
        $data = ['id' => $id];
        return $this->where($data)->find();
    }
}
