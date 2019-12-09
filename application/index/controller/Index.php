<?php
namespace app\index\controller;

use think\Controller;

class Index extends Controller
{
    public function index(){
        return 1;
    }
    public function login(){
        return $this->fetch();
    }

}
