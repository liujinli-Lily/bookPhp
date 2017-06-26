<?php
// +----------------------------------------------------------------------
// | snake
// +----------------------------------------------------------------------
// | Copyright (c) 2016~2022 http://baiyf.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: NickBai <1902822973@qq.com>
// +----------------------------------------------------------------------
namespace app\admin\controller;

use org\freebook\model\Bookorder AS BookorderModel;
use org\freebook\model\Grade AS GradeModel;
use org\freebook\model\Semester AS SemesterModel;
use org\freebook\model\Textbook AS TextbookModel;
use org\freebook\model\Publishing AS PublishingModel;
use think\Controller;
use think\Session;

class Bookorder extends Controller
{
    public function index()
    {
        return $this->fetch();
    }

    public function ajax_list(){
        if(request()->isAjax()) {
            $book_order_model = new BookorderModel();
            $grade_model = new GradeModel();
            $publishing_model=new PublishingModel();
            $id=Session::get('id');
            $result = $book_order_model ->getOrderAll(input('param.'),$id);
            foreach($result as $key => $val){
                $operate = [
                    '编辑' => url('admin/bookorder/editorder', ['id' => $val['id'],'tb_id'=>$val['tb_id']]),
                    '删除' => "javascript:j_delete('".$val['id']."')"
                ];
                if($val['order_time'] =="" || $val['order_time'] == null){
                    $val['order_time']='';
                }else{
                    $val['order_time']=date('Y-m-d H:i:s', $val['order_time']);
                }
                $result[$key]['operate'] = showOperate($operate);
                $result[$key]['grade'] = $grade_model->getById($val['grade']);
                $result[$key]['publishing'] = $publishing_model->get_title($val['publishing']);
            }
            $return['total'] = $book_order_model->getAllCount($id);  //总数据
            $return['rows'] = $result;
            return json($return);
       }
    }
    //添加
    public function addorder()
    {
        $book_order_model = new BookorderModel();
        if(request()->isPost()) {
            $result = $book_order_model->addorder(input('param.'));
            if(false === $result){
                return json(['code' =>2,'data'=>'','msg'=>'增加失败']);
            }else{
                return json(['code' =>1,'data'=>'','msg'=>'增加成功','url' => url('admin/bookorder/index')]);
            }
        }
        $bookList= $book_order_model->getAllbook();
        $this->assign('book_list',$bookList);
        $id=Session::get('id');
        $this->assign('userinfo',$id);

        $strat_time = date('Y')-5;
        $end_time = date('Y')+5;
        $years=[];
        $current=date('Y');
        for($i = $strat_time;$i<$end_time;$i++){
            $years[$i]=$i;
        }
        $this->assign('current',$current);
        $this->assign('years',$years);
        //年级
        $grade_model=new GradeModel();
        $grade_list=$grade_model->getAll();
        $this->assign('grade_list',$grade_list);

        //学期
        $semester_model=new SemesterModel();
        $semester_list=$semester_model->getAll();
        $this->assign('semester_list',$semester_list);
        //科目
        $subject_list=$book_order_model->getAllSubject();
        $this->assign('subject_list',$subject_list);

        return $this->fetch();
    }
    //删除
    public function delete_order()
    {
        if(request()->isAjax()){
            $book_order_model = new BookorderModel();
            $result = $book_order_model->delete_order(input());
            if(false === $result){
                return ['code' =>2,'data'=>'','msg'=>'删除失败'];
            }else{
                return ['code' =>1,'data'=>'','msg'=>'删除成功'];
            }
        }
    }
    //编辑
    public function editorder()
    {
        $book_order_model = new BookorderModel();
        if(request()->isPost()){
            $result = $book_order_model->editorder(input());
            if(false === $result){
                return json(['code' =>2,'data'=>'','msg'=>'修改失败']);
            }else{
                return json(['code' =>1,'data'=>'','msg'=>'修改成功', 'url' => url('admin/bookorder/index')]);
            }
        }

        $bookList= $book_order_model->getAllbook();
        $this->assign('book_list',$bookList);

        $user=Session::get('id');
        $this->assign('userinfo',$user);

        $id = input('param.id');
        $tb_id=input('param.tb_id');
        $info = $book_order_model ->get_id($id);
        $this ->assign('book_name',$tb_id);
        $this ->assign('info',$info);

        return $this->fetch();
    }
    public function getbookBygrade(){
        $textbook_model=new TextbookModel();
        if(request()->isPost()) {
            $result = $textbook_model->getbookBygrade(input('param.'));
            if($result){
                $this->ajaxMsg['code'] = 1;
                $this->ajaxMsg['msg'] = "查询成功";
                $this->ajaxMsg['resultList'] = $result;
            }else{
                $this->ajaxMsg['code'] = 2;
                $this->ajaxMsg['msg'] = "数据为空";
            }
            return json($this->ajaxMsg);
        }
    }
    /*
     * 根据年级及学期获取科目信息
     * */
    public function getSubjectBygrade(){
        $textbook_model=new TextbookModel();
        if(request()->isPost()) {
            $result = $textbook_model->getSubjectBygrade(input('param.'));
            if($result){
                $this->ajaxMsg['code'] = 1;
                $this->ajaxMsg['msg'] = "查询成功";
                $this->ajaxMsg['resultList'] = $result;
            }else{
                $this->ajaxMsg['code'] = 2;
                $this->ajaxMsg['msg'] = "数据为空";
            }
            return json($this->ajaxMsg);
        }
    }
    /*
     * 根据年级及学期,科目获取版别信息
     * */
    public function getPublishBysubject(){
        $textbook_model=new TextbookModel();
        if(request()->isPost()) {
            $result = $textbook_model->getPublishBysubject(input('param.'));
            if($result){
                $this->ajaxMsg['code'] = 1;
                $this->ajaxMsg['msg'] = "查询成功";
                $this->ajaxMsg['resultList'] = $result;
            }else{
                $this->ajaxMsg['code'] = 2;
                $this->ajaxMsg['msg'] = "数据为空";
            }
            return json($this->ajaxMsg);
        }
    }
    /*
     * 根据年级及学期,科目，版别获取书本信息
     * */
    public function getBookByPublish()
    {
        $textbook_model = new TextbookModel();
        if (request()->isPost()) {
            $result = $textbook_model->getBookByPublish(input('param.'));
            if ($result) {
                $this->ajaxMsg['code'] = 1;
                $this->ajaxMsg['msg'] = "查询成功";
                $this->ajaxMsg['resultList'] = $result;
            } else {
                $this->ajaxMsg['code'] = 2;
                $this->ajaxMsg['msg'] = "数据为空";
            }
            return json($this->ajaxMsg);
        }
    }
}