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

use org\freebook\model\Textbook AS TextbookModel;
use org\freebook\model\Grade AS GradeModel;
use org\freebook\model\Semester AS SemesterModel;
use org\freebook\model\Publishing AS PublishingModel;
use PHPExcel_IOFactory;
use PHPExcel;

use think\Controller;
use think\File;

class Textbook extends Controller
{
    public function index()
    {
        return $this->fetch();
    }
    public function ajax_sub_list(){
        if(request()->isAjax()) {
            $book_list_model= new TextbookModel();
            $grade_model=new GradeModel();
            $semester_model=new SemesterModel();
            $publishing_model=new PublishingModel();
            $result = $book_list_model ->getByTitleAll(input());
            foreach($result as $key => $val){
                $operate = [
                    '编辑' => url('admin/textbook/editbook', ['id' => $val['id']]),
                    '删除' => "javascript:book_del('".$val['id']."')"
                ];
                $result[$key]['grade'] = $grade_model->getById($val['grade']);
                $result[$key]['semester'] = $semester_model->getById($val['semester']);
                $result[$key]['publishing']=$publishing_model->get_title($val['publishing']);
                $result[$key]['operate'] = showOperate($operate);
            }
            $status=input('param.status');
            $return['total'] = $book_list_model->getAllCount($status);  //总数据
            $return['rows'] = $result;
            return json($return);
       }
    }
    //添加
    public function addbook()
    {
        if(request()->isPost()) {
            $text_book_model = new TextbookModel();
            $result = $text_book_model->addbook(input());
            if(false === $result){
                return json(['code' =>2,'data'=>'','msg'=>'增加失败']);
            }else{
                return json(['code' =>1,'data'=>'','msg'=>'增加成功', 'url' => url('admin/textbook/index')]);
            }
        }
        //科目
        $bookList = new TextbookModel();
        $subjectList= $bookList->getAllSubject();
        $this->assign('subject_list',$subjectList);
        //年级
        $grade_model=new GradeModel();
        $grade_list=$grade_model->getAll();
        $this->assign('grade_list',$grade_list);
        //学期
        $semester_model=new SemesterModel();
        $semester_list=$semester_model->getAll();
        $this->assign('semester_list',$semester_list);
        //版别
        $publishing_list=$bookList->getAllPub();
        $this->assign('publishing_list',$publishing_list);
        return $this->fetch();
    }
    //删除
    public function delete_book()
    {
        if(request()->isAjax()){
            $text_book_model = new TextbookModel();
            $result = $text_book_model->delete_book(input());
            if(false === $result){
                return json(['code' =>2,'data'=>'','msg'=>'删除失败']);
            }else{
                return json(['code' =>1,'data'=>'','msg'=>'删除成功']);
            }
        }
    }
    //编辑
    public function editbook()
    {
        if(request()->isPost()) {
            $text_book_model = new TextbookModel();
            $result = $text_book_model->editbook(input());
            if(false === $result){
                return json(['code' =>2,'data'=>'','msg'=>'修改失败']);
            }else{
                return json(['code' =>1,'data'=>'','msg'=>'修改成功', 'url' => url('admin/textbook/index')]);
            }
        }
        $bookList = new TextbookModel();
        $id = input('param.id');
        $info = $bookList ->get_id($id);
        $this ->assign('info',$info);

        //科目
        $subjectList= $bookList->getAllSubject();
        $this->assign('subject_list',$subjectList);
        //年级
        $grade_model=new GradeModel();
        $grade_list=$grade_model->getAll();
        $this->assign('grade_list',$grade_list);

        //学期
        $semester_model=new SemesterModel();
        $semester_list=$semester_model->getAll();
        $this->assign('semester_list',$semester_list);
        //版别
        $publishing_list=$bookList->getAllPub();
        $this->assign('publishing_list',$publishing_list);

        return $this->fetch();
    }

    public function upExecel(){
        //判断表格是否上传成功
        if($_FILES){
            $File= $_FILES['file'];
            $file = new File($File['tmp_name'],'rw');
            $file->setUploadInfo($File['name']);
            // 移动到框架应用根目录/public/uploads/ 目录下
            $info =$file->move(ROOT_PATH . 'public' . DS . 'uploads',true,true);
            if($info){
                // 成功上传后 获取上传信息
                /*echo $info->getExtension();echo $info->getSaveName();echo $info->getFilename();*/
                vendor("PHPExcel.PHPExcel");
                $file_name=$info->getPathname();
                if($info->getExtension() =='xlsx' || $info->getExtension() =='xlsm' )
                {
                    $objReader = PHPExcel_IOFactory::createReader('Excel2007');
                }
                else
                {
                    $objReader = PHPExcel_IOFactory::createReader('Excel5');
                }
                $objPHPExcel = $objReader->load($file_name,$encode='utf-8');
                $sheet = $objPHPExcel->getSheet(0);
                $highestRow = $sheet->getHighestRow(); // 取得总行数
                // $highestColumn = $sheet->getHighestColumn(); // 取得总列数

                //循环读取excel表格,读取一条,插入一条
                //j表示从哪一行开始读取 从第二行开始读取，因为第一行是标题不保存
                $text_book_model = new TextbookModel();
                $semester_model=new SemesterModel();
                $grade_model=new GradeModel();
                $item=[];
                for($j=2;$j<=$highestRow;$j++)
                {
                    $item['subject']= $objPHPExcel->getActiveSheet()->getCell("A".$j)->getValue();//获取A列的值
                    $item['publishing'] = $objPHPExcel->getActiveSheet()->getCell("C".$j)->getValue();//获取c(列的值
                    $item['title'] = $objPHPExcel->getActiveSheet()->getCell("D".$j)->getValue();//获取d列的值
                    $item['semester']= $objPHPExcel->getActiveSheet()->getCell("E".$j)->getValue();//获取e列的值
                    $item['grade'] = $objPHPExcel->getActiveSheet()->getCell("F".$j)->getValue();//获取d列的值
                    $item['content']= $objPHPExcel->getActiveSheet()->getCell("G".$j)->getValue();//获取e列的值

                    $item['subject'] = $text_book_model->getTitleBySubject(trim($item['subject']));
                    $item['publishing'] = $text_book_model->getTitleBypublish(trim($item['publishing']));
                    //$item['semester'] = $semester_model->getByTitle(trim($item['semester']));
                    //$item['grade'] = $grade_model->getTitleBygrade(trim($item['grade']));

                    $item['status']=1;
                    $newData = $item;

                    if(strstr($item['grade'],"至")){
                        $grade=explode("至",$item['grade']);
                        $min = $this->getGradeInt($grade[0]);
                        $max = $this->getGradeInt($grade[1]);

                        //echo $min.'---'.$max.'年级<br/>';
                        error_log($min.'---'.$max.'年级'."\r\n",3,RUNTIME_PATH.'/execl.log');
                        $msg = sprintf("开始循环%d到%d年假",$min,$max);
                        error_log("$msg\r\n",3,RUNTIME_PATH.'/execl.log');
                        for($i=$min;$i<=$max;$i++){
                            $newData['grade'] = $i;
                            if(strstr($item['semester'],'全一册')){

                                //插入两次数据,书号一致
                                $msg = sprintf("添加%d年假上册",$i);
                                error_log("$msg\r\n",3,RUNTIME_PATH.'/execl.log');
                                $newData['isbn']=$j.$i.'1';
                                $newData['semester']=1;
                                $result =$text_book_model->addbook($newData);
                                $newData['semester']=2;
                                $msg = sprintf("添加%d年假下册",$i);
                                error_log("$msg\r\n",3,RUNTIME_PATH.'/execl.log');
                                $result =$text_book_model->addbook($newData);
                            }
                            //上下册
                            if(strstr($item['semester'],'上下册')){
                                $msg = sprintf("添加%d年假上册",$i);
                                error_log("$msg\r\n",3,RUNTIME_PATH.'/execl.log');
                                $newData['isbn']=$j.$i.'1';
                                $newData['semester']=1;
                                $result = $text_book_model->addbook($newData);
                                $msg = sprintf("添加%d年假下册",$i);
                                error_log("$msg\r\n",3,RUNTIME_PATH.'/execl.log');
                                $newData['isbn']=$j.$i.'2';
                                $newData['semester']=2;
                                $result = $text_book_model->addbook($newData);
                            }
                        }
                    }else if(strlen($item['grade'])==9){
                        //单个年级
                        $newData['grade'] = $grade_model->getTitleBygrade(trim($item['grade']));
                        if(trim($item['semester']=='下册')){
                            //下册
                            $newData['semester']=2;
                            $newData['isbn']=$j.$newData['grade'].'2';
                            $result =$text_book_model->addbook($newData);
                        }elseif(trim($item['semester']=='上册')){
                            $newData['semester']=1;
                            $newData['isbn']=$j.$newData['grade'].'1';
                            $result =$text_book_model->addbook($newData);
                        }else {
                            //上下册  插入两次数据,书号不一致
                            $newData['isbn']=$j.$newData['grade'].'1';
                            $newData['semester']=1;
                            $result =$text_book_model->addbook($newData);
                            $newData['isbn']=$j.$newData['grade'].'2';
                            $newData['semester']=2;
                            $result =$text_book_model->addbook($newData);
                        }
                    }

                }
                if(false === $result){
                    return json(['code' =>2,'data'=>'','msg'=>'增加失败']);
                    exit;
                }else{
                    return json(['code' =>1,'data'=>'','msg'=>'增加成功']);
                }
            }else{
                // 上传失败获取错误信息
                echo $file->getError();
            }
            exit;
        }
    }
    private function getGradeInt($str){
        if (false == $str) return false;
        switch ($str){
            case "一年级" : $min=1 ;break;
            case "二年级" : $min=2 ;break;
            case "三年级" : $min=3 ;break;
            case "四年级" : $min=4 ;break;
            case "五年级" : $min=5 ;break;
            case "六年级" : $min=6 ;break;
            case "七年级" : $min=7 ;break;
            case "八年级" : $min=8 ;break;
            case "九年级" : $min=9 ;break;
        }
        return $min;
    }
}