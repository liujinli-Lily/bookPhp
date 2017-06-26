<?php
/**
 * Created by PhpStorm.
 * User: liuwen
 * Date: 17/6/5
 * Time: 上午9:10
 */

namespace app\admin\controller;


use think\Controller;
use org\freebook\model\Bookorder AS BookorderModel;
use org\freebook\model\Grade AS GradeModel;
use org\freebook\model\Semester AS SemesterModel;
use app\admin\model\UserModel;
use org\freebook\model\Dictcity AS DictcityModel;
use org\freebook\model\Subjectbook AS SubjectbookModel;
use org\freebook\model\Textbook AS TextbookModel;
use org\freebook\model\Publishing AS PublishingModel;
use PHPExcel_IOFactory;
use PHPExcel;
use think\Session;

class Report extends Controller
{
    public function index()
    {
        $user_model=new UserModel();
        $id=Session::get('id');
        $typeid=$user_model->where(['id'=>$id])->value('typeid');
        if (request()->isAjax()) {
            $book_order_model= new BookorderModel();
            $grade_model=new GradeModel();
            $semester_model=new SemesterModel();
            $publishing_model=new PublishingModel();
            $result = $book_order_model ->getList(input(),$typeid,$id);
            foreach($result as $key => $val){
                $val['publishing']=$publishing_model->get_title($val['publishing']);
                $result[$key]['grade'] = $grade_model->getById($val['grade']);
                $result[$key]['semester'] = $semester_model->getById($val['semester']);
            }
            $return['total'] = $book_order_model->getAllCount($id);  //总数据
            $return['rows'] = $result;
            return json($return);
        }
        $dictcity_model=new DictcityModel();
        $where=['parent_id'=>420000];
        $city_list=$dictcity_model->getAll($where);
        $this->assign('city_list',$city_list);

        $subjectbook_model=new SubjectbookModel();
        $subject_list=$subjectbook_model->getAll();
        $this->assign('subject_list',$subject_list);

        $userinfo=$user_model->getOneUser($id);
        $this->assign('userinfo',$userinfo);
        return $this->fetch();
    }
    public function xsl(){
//        $xlsCell1 = input('post');
        $xlsCell1 = input('param.');

       $xlsCell2 = [];
        foreach($xlsCell1 as $val){
            if(is_array($val)){
                $xlsCell2 = array_values($val);
            }
        }
      /*  print_r($xlsCell2);
        die();*/
//       $xlsCell2 = array(
//                        array('organic','单位'),
//                        array('grade','年级'),
//                        array('semester','学期'),
//                        array('title','书名'),
//                        array('publishing','版别'),
//                        array('people_num','在校学生数'),
//                        array('order_num','征订教科书册数'),
//                        array('total_price','教科书金额')
//                    );

       // var_dump($xlsCell2);
        return $xlsCell2;
    }
    public function exportExcel()
    {

        $xlsCell1 = input('param.cell');
        $arr = explode(',',$xlsCell1);
        $arr1=[];
        foreach($arr as $k => $v){
            if($k == 0 || $k %2 == 0){
                $arr1[$k] = array(
                    $k => $arr[$k],
                    $k+1 => $arr[$k+1]
                );
            }
        }
        $xlsCell2 = [];
        foreach($arr1 as $val){
            if(is_array($val)){
                $xlsCell2[] = array_values($val);
            }
        }


        $xlsName  = "Book_order";
        $book_order_model= new BookorderModel();
        $grade_model=new GradeModel();
        $semester_model=new SemesterModel();
        $user_model=new UserModel();
        $id=Session::get('id');
        $typeId=$user_model->where(['id'=>$id])->value('typeId');
        $result = $book_order_model ->getList(input(),$typeId,$id);

        foreach($result as $key => $val){
            $result[$key]['grade'] = $grade_model->getById($val['grade']);
            $result[$key]['semester'] = $semester_model->getById($val['semester']);
        }
        $this->exportExcel_r($xlsName,$xlsCell2,$result);
    }
    public function exportExcel_r($expTitle,$expCellName,$expTableData){
        $xlsTitle = iconv('utf-8', 'gb2312', $expTitle);//文件名称
        $fileName = date('_YmdHis');//or $xlsTitle 文件名称可根据自己情况设定
        $cellNum = count($expCellName);
        $dataNum = count($expTableData);
        vendor("PHPExcel.PHPExcel");
        $objPHPExcel = new PHPExcel();
        $cellName = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ');
        for($i=0;$i<$cellNum;$i++){
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($cellName[$i].'1', $expCellName[$i][1]);
        }
        // Miscellaneous glyphs, UTF-8
        for($i=0;$i<$dataNum;$i++){
            for($j=0;$j<$cellNum;$j++){
                $objPHPExcel->getActiveSheet()->setCellValue($cellName[$j].($i+2), $expTableData[$i][$expCellName[$j][0]]);
            }
        }
        header('pragma:public');
        header('Content-type:application/vnd.ms-excel;charset=utf-8;name="'.$xlsTitle.'.xls"');
        header("Content-Disposition:attachment;filename=$fileName.xls");//attachment新窗口打印inline本窗口打印
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        exit;
    }
    public function export(){
        // Create new PHPExcel object
        vendor("PHPExcel.PHPExcel");
        $objPHPExcel = new PHPExcel();
        // Set document properties
        $objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
            ->setLastModifiedBy("Maarten Balliauw")
            ->setTitle("Office 2007 XLSX Test Document")
            ->setSubject("Office 2007 XLSX Test Document")
            ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
            ->setKeywords("office 2007 openxml php")
            ->setCategory("Test result file");
        // Add some data
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', 'Hello')
            ->setCellValue('B2', 'world!')
            ->setCellValue('C1', 'Hello')
            ->setCellValue('D2', 'world!');
        // Miscellaneous glyphs, UTF-8
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A4', 'Miscellaneous glyphs')
            ->setCellValue('A5', 'éàèùâêîôûëïüÿäöüç')
            ->setCellValue('A6', '免费教材系统');
        // Rename worksheet
        $objPHPExcel->getActiveSheet()->setTitle('Simple');
        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);
        // Redirect output to a client’s web browser (Excel5)
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="01simple.xls"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');
        // If you're serving to IE over SSL, then the following may be needed
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        exit;
    }
    /**
     * 获取区域
     * @param $cityId
     * @return bool
     */
    public function getArea(){
        $dictcity_model=new DictcityModel();
        if(request()->isPost()) {
            $city_id=input('param.city_id');
            $result=$dictcity_model->getArea($city_id);
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

    public function getSubject(){
        $textbook_model=new TextbookModel();
        if(request()->isPost()) {
            $result=$textbook_model->getSubject(input());
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
}