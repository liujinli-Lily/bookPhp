<?php
namespace app\test\controller;

use org\freebook\model\Textbook;
use think\Controller;

class Index extends Controller
{
    public function index()
    {
        $test_book = new Textbook();
//        $request['searchText'] = '';
        $test_book->getByAll();
    }
}
