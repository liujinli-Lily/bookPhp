<?php
/**
 * Created by PhpStorm.
 * User: liujinli
 * Date: 2017/6/3
 * Time: 13:45
 */

namespace org\freebook\model;


class Grade
{
    protected $data = [];

    public function __construct()
    {
        $this->data = [1=>'一年级',2=>'二年级',3=>'三年级',4=>'四年级',5=>'五年级',6=>'六年级',7=>'七年级',8=>'八年级',9=>'九年级'];
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
    public function getTitleBygrade($val){
        if(array_search($val,$this->data)){
            return array_search($val,$this->data);
        }
        return NULL;
        //return $result=$this->where(['title'=>$title])->value('id');
    }
}