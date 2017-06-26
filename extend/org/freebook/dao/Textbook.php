<?php
/**
 * Created by PhpStorm.
 * User: liuwen
 * Date: 17/6/1
 * Time: 上午9:45
 */

namespace org\freebook\dao;

use think\Model;

class Textbook extends Model{
    protected  $table = 'fb_textbook';

    public function getByTitleAll($offset=0,$limit=10,$order='id desc',$title,$status)
    {
        $result = $this ->where(['title'=> ['like', '%' .$title. '%'],'status'=>$status])->limit($offset,$limit)->order('id desc') ->select();
        return $result;
    }
    public function get_count($status){
        return $this->where(['status'=>$status])->count();
    }
    public function addbook($param){
        //var_dump($param);return;
        $result = $this -> save($param);
        //error_log($this->getLastSql()."\r\n",3,RUNTIEM_PATH.'sql.log');
        return $result;
    }
    //编辑
    public function editbook($param,$id)
    {
        $result = $this -> save($param,['id'=>$id]);
        return $result;
    }
    public function delete_book($id){
        $result = $this -> destroy($id);
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