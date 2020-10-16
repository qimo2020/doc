<?php

namespace app\doc\admin;
use app\system\admin\Base;

class Index extends Base
{

    public function index(){
        echo '欢迎使用 cms-admin-index-index';
        return $this->view();
    }

    public function welcome(){
        return $this->view();
    }
}
