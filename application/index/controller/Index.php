<?php
namespace app\index\controller;

use org\freebook\model\Textbook;
use think\Controller;

class Index extends Controller
{
    public function index()
    {
        return $this->fetch('index');

    }
}
