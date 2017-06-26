<?php
/**
 * Created by PhpStorm.
 * User: liuwen
 * Date: 17/6/1
 * Time: 上午9:46
 */

namespace org\freebook\model;

use org\freebook\dao\Textbook AS TextbookDao;
use org\freebook\dao\Subjectbook AS SubjectbookDao;
use org\freebook\dao\Publishing AS PublishingDao;


class Textbook extends BaseModel{

    public function getByTitleAll($request)
    {
        $param = array(
            'offset' => 0,
            'limit' => 10,
            'order' => 'id desc',
            'status'=>'1',
        );
        $param = $this->getParameter($param,$request);
        $textbook_dao = new TextbookDao();
        $limit = $param['limit'];
        $offset = ($param['offset'] - 1) * $limit;
        $result = $textbook_dao->getByTitleAll($offset,$limit,$param['order'],$request['searchText'],$request['status']);
        $subject_book_dao = new SubjectbookDao();
        foreach($result as $key => $val){
            $result[$key]['subject'] = $subject_book_dao->get_title($val['subject']);
        }
        return $result;
    }
    public function getAllCount($status){
        $textbook_dao = new TextbookDao();
        return $result=$textbook_dao->get_count($status);
    }
    public function addbook($request){
        error_log(json_encode($request)."\r\n",3,RUNTIME_PATH.'/execl.log');
        //return;
        $textbook_dao = new TextbookDao();
        if(isset($request['data'])){
            $param = parseParams($request['data']);
            $result = $textbook_dao->addbook($param);
        }else{

            $result = $textbook_dao->addbook($request);
            //echo $textbook_dao->getLastSql();
        }
        return $result;
    }
    public function editbook($request){
        $param = parseParams($request['data']);
        $textbook_dao = new TextbookDao();
        $result = $textbook_dao->editbook($param,$param['id']);
        return $result;
    }
    public function delete_book($request){
        $textbook_dao = new TextbookDao();
        $result = $textbook_dao->delete_book($request['id']);
        return $result;
    }
    public function getAllSubject(){
        $subject_book_Dao = new SubjectbookDao();
        return $result=$subject_book_Dao->field('id, title')->select();
    }
    public function getAllPub(){
        $publishing_dao = new PublishingDao();
        return $result=$publishing_dao->field('id, title')->select();
    }
    public function get_id($id){
        $textbook_dao = new TextbookDao();
        $result = $textbook_dao->get_id($id);
        return $result;
    }
    public function getbookBygrade($request)
    {
        $data = [
            'grade' => $request['grade'],
            'semester' => $request['semester'],
        ];
        $text_book_dao = new TextbookDao();
        $result=$text_book_dao->where($data)->select();
        return $result;
    }

    public function getSubjectBygrade($request)
    {
        $data = [
            'grade' => $request['grade'],
            'semester' => $request['semester'],
            'fb_book_subject.status'=>1
        ];
        $subject_book_dao = new SubjectbookDao();
        $field='fb_book_subject.*';
        $result=$subject_book_dao->field($field)
            ->join(['fb_textbook' => 'sub'],'sub.subject = fb_book_subject.id','left')
            ->where($data)
            ->group('fb_book_subject.id')
            ->select();
        return $result;
    }
    public function getPublishBysubject($request)
    {
        $data = [
            'grade' => $request['grade'],
            'semester' => $request['semester'],
            'subject'=>$request['subject'],
        ];
        $publishing_dao = new PublishingDao();
        $field='book_publishing.*';
        $result=$publishing_dao->field($field)
            ->join(['fb_textbook' => 'pub'],'pub.publishing = book_publishing.id','left')
            ->where($data)
            ->group('book_publishing.id')
            ->select();
        return $result;
    }
    public function getBookByPublish($request)
    {
        $data = [
            'grade' => $request['grade'],
            'semester' => $request['semester'],
            'subject'=>$request['subject'],
            'publishing'=>$request['publishing'],
            'fb_textbook.status'=>1
        ];
        $textbook_dao = new TextbookDao();
        $result=$textbook_dao->where($data)->group('fb_textbook.id')->select();
        return $result;
    }
    public function getSubject($request)
    {
        $data = [
            'subject'=>$request['subject'],
        ];
        $publishing_dao = new PublishingDao();
        $field='book_publishing.*';
        $result=$publishing_dao->field($field)
            ->join(['fb_textbook' => 'pub'],'pub.publishing = book_publishing.id','left')
            ->where($data)
            ->group("publishing")
            ->select();
        return $result;
    }

    //导入时查找科目信息
    public function getTitleBySubject($title){
        $subject_book_dao = new SubjectbookDao();
        $data=[];
        $data['title']=$title;
        $result=$subject_book_dao->where($data)->value('id');

        if($result === NULL){
            if($data['title'] !==''){
                $data['status']=1;
                $insert=$subject_book_dao->insert_sub($data);

                if(false === $insert){
                    exit;
                }else{
                    return $result=$subject_book_dao->where($data)->value('id');
                }
            }
        }else{
            return $result;
        }
    }
    //导入时查找版别信息
    public function getTitleBypublish($title){
        $publishing_dao = new PublishingDao();
        $data=[
            'title'=>$title
        ];
        $result=$publishing_dao->where($data)->value('id');
        if($result === NULL){
            if($data['title'] !==''){
                $insert=$publishing_dao->insert($data);
                if(false === $insert){
                    exit;
                }else{
                    return $result=$publishing_dao->where($data)->value('id');
                }
            }
        }else{
            return $result;
        }
    }
}