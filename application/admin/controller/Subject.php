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

use org\freebook\model\Subjectbook;
use think\Controller;

class Subject extends Controller
{
    public function index()
    {
        return $this->fetch();
    }

    public function ajax_sub_list(){
        if(request()->isAjax()) {
            $subjectList = new Subjectbook();
            $result = $subjectList ->getSubjectAll(input('param.'));
            foreach($result as $key => $val){
                $operate = [
                    '编辑' => url('admin/subject/subedit', ['id' => $val['id']]),
                    '删除' => "javascript:subjectDel('".$val['id']."')"
                ];
                $result[$key]['operate'] = showOperate($operate);
            }
            $status=input('param.status');
            $return['total'] = $subjectList->getAllCount($status);  //总数据
            $return['rows'] = $result;
            return json($return);
       }
    }
    //添加
    public function subadd()
    {
        if(request()->isPost()) {
            $subject_model = new Subjectbook();
            $result = $subject_model->setInsert(input());
            if(false === $result){
                return json(['code' =>2,'data'=>'','msg'=>'增加失败']);
            }else{
                return json(['code' =>1,'data'=>'','msg'=>'增加成功','url' => url('admin/subject/index')]);
            }
        }
        return $this->fetch();
    }
    //删除
    public function subjectDel()
    {
        if(request()->isAjax()){
            $subject_model = new Subjectbook();
            $result = $subject_model->delete_sub(input());
            if(false === $result){
                return json(['code' =>2,'data'=>'','msg'=>'删除失败']);
            }else{
                return json(['code' =>1,'data'=>'','msg'=>'删除成功']);
            }
        }
    }
    //编辑
    public function subedit()
    {
        $subject_model = new Subjectbook();
        if(request()->isPost()){
            $result = $subject_model->updateSub(input());
            if(false === $result){
                return  json(['code' =>2,'data'=>'','msg'=>'修改失败']);
            }else{
                return  json(['code' =>1,'data'=>'','msg'=>'修改成功' , 'url' => url('admin/subject/index')]);
            }
        }
        $id = input('param.id');
        $info = $subject_model ->get_id($id);
        $this ->assign('info',$info);
        return $this->fetch();
    }
}