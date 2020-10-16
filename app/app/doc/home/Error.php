<?php declare(strict_types=1);
namespace app\doc\home;
defined('IN_SYSTEM') or die('Access Denied');
use think\exception\HttpException;
use app\doc\model\Doc;
use app\doc\model\DocMember;
class Error extends Base
{
    public function __call($method, $args)
    {
        $username = strtolower($this->request->controller());
        if(null === DocMember::where('name', $username)->find()){
            throw new HttpException(404, '当前页面不存在');
        }
        if(null === Doc::where('name', $method)->find()){
            throw new HttpException(404, '当前页面不存在');
        }
        return $this->view('index/doc');
    }

}
