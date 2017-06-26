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

use org\freebook\model\Publishing as PublishingModel;
use think\Controller;

class Publishing extends Controller
{
    public function index()
    {
        if(request()->isAjax()) {
            $publishing_model = new PublishingModel();
            $result = $publishing_model ->getAll(input('param.'));
            foreach($result as $key => $val){
                $operate = [
                    '编辑' => url('admin/publishing/pubedit', ['id' => $val['id']]),
                    '删除' => "javascript:pub_delete('".$val['id']."')"
                ];
                $result[$key]['operate'] = showOperate($operate);
            }
            $return['total'] = $publishing_model->getAllCount();  //总数据
            $return['rows'] = $result;
            return json($return);
        }
        return $this->fetch();
    }

    //添加
    public function pubadd()
    {
        if(request()->isPost()) {
            $publishing_model = new PublishingModel();
            $result = $publishing_model->setInsert(input());
            if(false === $result){
                return json(['code' =>2,'data'=>'','msg'=>'增加失败']);
            }else{
                return json(['code' =>1,'data'=>'','msg'=>'增加成功','url' => url('admin/publishing/index')]);
            }
        }
        return $this->fetch();
    }
    //删除
    public function pub_delete()
    {
        if(request()->isAjax()){
            $publishing_model = new PublishingModel();
            $result = $publishing_model->pub_delete(input());
            if(false === $result){
                return json(['code' =>2,'data'=>'','msg'=>'删除失败']);
            }else{
                return json(['code' =>1,'data'=>'','msg'=>'删除成功']);
            }
        }
    }
    //编辑
    public function pubedit()
    {
        $publishing_model = new PublishingModel();
        if(request()->isPost()){
            $result = $publishing_model->update_pub(input());
            if(false === $result){
                return  json(['code' =>2,'data'=>'','msg'=>'修改失败']);
            }else{
                return  json(['code' =>1,'data'=>'','msg'=>'修改成功' , 'url' => url('admin/publishing/index')]);
            }
        }
        $id = input('param.id');
        $info = $publishing_model ->get_id($id);
        $this ->assign('info',$info);
        return $this->fetch();
    }
}