<?php
/**
 * Created by PhpStorm.
 * User: GGJRW
 * Date: 2017/6/3
 * Time: 14:07
 */

namespace org\freebook\model;


class Semester
{
    protected $data = [];

    public function __construct()
    {
        $this->data = [1=>'上学期',2=>'下学期'];
    }
    public function getAll(){
        return $this->data;
    }
    public function getById($id){
        if(array_key_exists($id,$this->data)){
            return $this->data[$id];
        }
        return NULL;
    }
    public function getByTitle($val){
        if(array_search($val,$this->data)){
            return array_search($val,$this->data);
        }
        return NULL;
    }
}