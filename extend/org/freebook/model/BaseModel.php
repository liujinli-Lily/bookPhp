<?php
/**
 * Created by PhpStorm.
 * User: GGJRW
 * Date: 2017/6/2
 * Time: 9:29
 */
namespace org\freebook\model;

class BaseModel {
    protected function getParameter(array $search,array $request){
        $data = [];
        foreach($search as $key => $val){
            $data[$key] = array_key_exists($key,$request) ?  $request[$key] :$val;
        }
        return $data;
    }

}