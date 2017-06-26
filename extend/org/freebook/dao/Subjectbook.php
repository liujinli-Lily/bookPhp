<?php
/**
 * Created by PhpStorm.
 * User: liuwen
 * Date: 17/6/1
 * Time: 上午9:45
 */

namespace org\freebook\dao;

use think\Model;

class Subjectbook extends Model{
    protected  $table = 'fb_book_subject';

    public function getSubjectAll($offset=0,$limit=10,$order='id desc',$title,$status)
    {
        return $this ->where(['title'=> ['like', '%' .$title. '%'],'status'=>$status])->limit($offset,$limit)->order($order) ->select();
    }
    public function get_subject_count($status){
        return $this->where(['status'=>$status])->count();
    }
    //增加科目
    public function insert_sub($param)
    {
        $result = $this ->save($param);
        return $result;
    }
    //删除
    public function delete_sub($id)
    {
        $result = $this ->destroy($id);
        return $result;
    }
    //编辑
    public function update_sub($param,$id)
    {
        $result = $this ->save($param,['id'=>$id]);
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