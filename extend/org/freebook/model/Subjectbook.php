<?php
/**
 * Created by PhpStorm.
 * User: liuwen
 * Date: 17/6/1
 * Time: 上午9:46
 */

namespace org\freebook\model;

use org\freebook\dao\Subjectbook AS SubjectbookDao;

class Subjectbook extends BaseModel{

    public function getSubjectAll($request)
    {
       $param = array(
            'offset' => 0,
            'limit' => 10,
            'order' => 'id desc',
            'status'=>1,
        );
        $param = $this->getParameter($param,$request);
        $subject_dao = new SubjectbookDao();
        $limit = $param['limit'];
        $offset = ($param['offset'] - 1) * $limit;
        $result = $subject_dao->getSubjectAll($offset,$limit,$param['order'],$request['searchText'],$request['status']);
        return $result;
    }
    public function getAll()
    {
        $subject_dao = new SubjectbookDao();
        $result = $subject_dao->where(['status'=>1])->select();
        return $result;
    }
    public function getAllCount($status){
        $subject_dao = new SubjectbookDao();
        return $result=$subject_dao->get_subject_count($status);
    }
    public function setInsert($request){
        $param = parseParams($request['data']);
        $SubjectDao = new SubjectbookDao();
        $result = $SubjectDao->insert_sub($param);
        return $result;
    }
    public function updateSub($request){
        $param = parseParams($request['data']);
        $SubjectDao = new SubjectbookDao();
        $result = $SubjectDao->update_sub($param,$param['id']);
        return $result;
    }
    public function delete_sub($request){
        $SubjectDao = new SubjectbookDao();
        $result = $SubjectDao->delete_sub($request['id']);
        return $result;
    }
    public function get_id($id)
    {
        $SubjectDao = new SubjectbookDao();
        $result = $SubjectDao->get_id($id);
        return $result;
    }

}