<?php
/**
 * Created by PhpStorm.
 * User: liuwen
 * Date: 17/6/1
 * Time: 上午9:46
 */

namespace org\freebook\model;

use org\freebook\dao\Bookorder AS BookorderDao;
use org\freebook\dao\Textbook AS TextbookDao;
use org\freebook\dao\Subjectbook AS SubjectbookDao;

class Bookorder extends BaseModel{

    public function getOrderAll($request,$id)
    {
       $param = array(
            'offset' => 0,
            'limit' => 10,
            'order' => 'id desc'
        );
        $param = $this->getParameter($param,$request);
        $book_order_dao = new BookorderDao();
        $subject_book_dao=new SubjectbookDao();
        $limit = $param['limit'];
        $offset = ($param['offset'] - 1) * $limit;
        $field='fb_book_order.*,fb_textbook.grade,fb_textbook.subject,fb_textbook.publishing,fb_textbook.title';
        $result = $book_order_dao->field($field)
            ->join(['fb_textbook'=>'t'],'t.id = fb_book_order.tb_id','left')
            ->where(['user_id'=>$id])
            ->order('id desc')->limit($offset,$limit)
            ->select();
        //$result = $book_order_dao->getOrderAll($offset,$limit,$param['order']);
        $text_book_dao=new TextbookDao();
        foreach($result as $key => $val){
            $result[$key]['tb_id'] = $text_book_dao->get_title($val['tb_id']);
            $val['subject']=$subject_book_dao->get_title($val['subject']);
        }
        return $result;
    }
    //报表查询
    public function getList($request,$typeid,$id){
        $book_order_dao = new BookorderDao();
        $field='u.organic,t.grade,t.semester,t.subject,t.publishing,t.title,o.people_num,o.order_num,t.price*o.order_num as total_price';
        $where=[];
        if($request !=[]){
            if(isset($request['data'])){
                $request = parseParams($request['data']);
            }
            if($request['city_id'] !=""){
                $where['city_id']=$request['city_id'];
            }
            if($request['area_id'] !=""){
                $where['area_id']=$request['area_id'];
            }
            if($request['grade'] !=""){
                $where['grade']=array('in',$request['grade']);
            }
            if($request['subject'] !=""){
                $where['subject']=array('in',$request['subject']);
            }
            if(isset($request['publishing']) && $request['publishing'] !=""){
                $where['publishing']=$request['publishing'];
            }
        }
        $where1=[];
        if($typeid==3){
            $where1=[
                'u.id' => $id,
                'u.parent_id' => $id,
            ];
        }else if($typeid==4){
            $where1=[
                'u.id' => $id,
            ];
        }else{
        }
        $result = $book_order_dao->alias('o')->field($field)
            ->join(['fb_textbook'=>'t'],'t.id = o.tb_id','inner')
            ->join(['snake_user'=>'u'],'u.id = o.user_id','inner')
            ->where($where)
            ->whereOr($where1)
            ->order('user_id,grade,semester,order_time')
            ->select();
       //echo $book_order_dao->getLastSql();exit;
        return $result;
    }
    public function getAllCount($id){
        $book_order_dao = new BookorderDao();
        return $result=$book_order_dao->get_order_count($id);
    }
    public function addorder($request){
        $param = parseParams($request['data']);
        $param['order_time']=time();
        $book_order_dao = new BookorderDao();
        $result = $book_order_dao->addorder($param);
        return $result;
    }
    public function editorder($request){
        $param = parseParams($request['data']);
        $book_order_dao = new BookorderDao();
        $result = $book_order_dao->editorder($param,$param['id']);
        return $result;
    }
    public function delete_order($request){
        $book_order_dao = new BookorderDao();
        $result = $book_order_dao->delete_order($request['id']);
        return $result;
    }
    public function get_id($id)
    {
        $book_order_dao = new BookorderDao();
        $result = $book_order_dao->get_id($id);
        return $result;
    }
    public function getAllbook(){
        $text_book_dao = new TextbookDao();
        return $result=$text_book_dao->field('id, title')->select();
    }
    public function getAllSubject(){
        $subject_book_dao = new SubjectbookDao();
        return $result=$subject_book_dao->field('id, title')->select();
    }

}