<?php declare(strict_types=1);
namespace app\doc\home;
defined('IN_SYSTEM') or die('Access Denied');
use think\exception\HttpException;
use app\doc\model\Doc;
class Write extends Base
{
    public function __call($method, $args)
    {
        $member = session('member');
        if (!$member) {
            return $this->response(0, '未登录', '/doc');
        }
        if(empty($args) || !isset($args['name'])){
            throw new HttpException(404);
        }
        $book = Doc::where('name', $args['name'])->find();
//        if(null === $book){
//            return redirect('/doc');
//        }

        return $this->view('write/index');
    }



}
