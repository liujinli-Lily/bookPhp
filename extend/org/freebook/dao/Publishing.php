<?php
/**
 * Created by PhpStorm.
 * User: liuwen
 * Date: 17/6/1
 * Time: 上午9:45
 */

namespace org\freebook\dao;

use think\Model;

class Publishing extends Model{
    protected  $table = 'book_publishing';

    public function getAll($offset=0,$limit=10,$order='id desc',$title)
    {
        return $this ->where(['title'=> ['like', '%' .$title. '%']])->limit($offset,$limit)->order($order) ->select();
    }
    public function get_count(){
        return $this->count();
    }
    //增加科目
    public function insert($param)
    {
        $result = $this -> save($param);
        return $result;
    }
    //删除
    public function pub_delete($id)
    {
        $result = $this -> destroy($id);
        return $result;
    }
    //编辑
    public function updateById($param,$id)
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