<?php

namespace app\doc\admin;
use app\system\admin\Base;

class Error extends Base
{
    public function __call($method, $args)
    {
        echo '欢迎使用 doc 后台';
    }
}
