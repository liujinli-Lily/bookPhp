<?php
/**
 * Created by PhpStorm.
 * User: liuwen
 * Date: 17/6/1
 * Time: 上午9:46
 */

namespace org\freebook\model;

use org\freebook\dao\Publishing AS PublishingDao;

class Publishing extends BaseModel{

    public function getAll($request)
    {
       $param = array(
            'offset' => 0,
            'limit' => 10,
            'order' => 'id desc'
        );
        $param = $this->getParameter($param,$request);
        $publishing_dao = new PublishingDao();
        $limit = $param['limit'];
        $offset = ($param['offset'] - 1) * $limit;
        $result = $publishing_dao->getAll($offset,$limit,$param['order'],$request['searchText']);
        return $result;
    }
    public function getAllCount(){
        $publishing_dao = new PublishingDao();
        return $result=$publishing_dao->get_count();
    }
    public function setInsert($request){
        $param = parseParams($request['data']);
        $publishing_dao = new PublishingDao();
        $result = $publishing_dao->insert($param);
        return $result;
    }
    public function update_pub($request){
        $param = parseParams($request['data']);
        $publishing_dao = new PublishingDao();
        $result = $publishing_dao->updateById($param,$param['id']);
        return $result;
    }
    public function pub_delete($request){
        $publishing_dao = new PublishingDao();
        $result = $publishing_dao->pub_delete($request['id']);
        return $result;
    }
    public function get_id($id)
    {
        $publishing_dao = new PublishingDao();
        $result = $publishing_dao->get_id($id);
        return $result;
    }
    public function get_title($id){
        $publishing_dao = new PublishingDao();
        $result = $publishing_dao->get_title($id);
        return $result;
    }

}