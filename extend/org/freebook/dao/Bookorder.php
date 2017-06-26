<?php
/**
 * Created by PhpStorm.
 * User: liuwen
 * Date: 17/6/1
 * Time: 上午9:45
 */

namespace org\freebook\dao;

use think\Model;

class Bookorder extends Model{
    protected  $table = 'fb_book_order';

    public function getOrderAll($offset=0,$limit=10,$order='id desc',$id)
    {
        return $this ->limit($offset,$limit)->where(['id'=>$id])->order($order) ->select();
    }
    public function get_order_count($id){
        return $this->where(['id'=>$id])->count();
    }
    //增加科目
    public function addorder($param)
    {
        $result = $this -> save($param);
        return $result;
    }
    //删除
    public function delete_order($id)
    {
        $result = $this -> destroy($id);
        return $result;
    }
    //编辑
    public function editorder($param,$id)
    {
        $result = $this -> save($param,['id'=>$id]);
        return $result;
    }
    public function get_id($id){
        $data = ['id' => $id];
        return $this->where($data)->find();
    }
    public function get_title($id){
        $data = ['id' => $id];
        return $this->where($data)->value('title');
    }
}