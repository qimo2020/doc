<?php declare(strict_types=1);
namespace app\doc\home;
defined('IN_SYSTEM') or die('Access Denied');
class Index extends Base
{
    public function index(){
        $docs = \app\doc\model\Doc::where('status', 1)->withJoin('member', 'left')->select();
        $this->assign('docs', $docs);
        return $this->view();
    }

}
