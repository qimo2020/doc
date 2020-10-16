<?php declare(strict_types=1);

namespace app\doc\api;
defined('IN_SYSTEM') or die('Access Denied');

use app\common\controller\Common;
use app\doc\model\Doc;
use app\doc\model\DocCatalog;

class Catalog extends Common
{

    public function __call($method, $args)
    {
        return json(['code' => 0, 'msg' => '404']);
    }

    /**
     * @title 默认
     * @desc 默认
     * @url doc/api/catalog/index
     * @method GET
     * @test 1
     */
    public function index()
    {
//        $member = session('member');
//        if (!$member) {
//            return json(['code'=>0, 'msg'=>'未登录']);
//        }
        $params = $this->request->param();
        $books = Doc::where('name', $params['book'])->find();
        if($books === null){
            return json(['code' => 0, 'msg' => '不存在']);
        }
        $data = DocCatalog::where(['did' => $books['id']])->select()->toArray();
        $data = $this->getTree($data);
        return json(['code' => 1, 'data' => $data]);
    }

    private function getTree($data, $pid=0){
        $result = [];
        $i = 0;
        foreach ($data as $k=>$v){
            if($v['pid'] == $pid){
                $result[$i]['id'] = $v['id'];
                $result[$i]['name'] = $v['title'];
                $result[$i]['file'] = $v['file'];
                $result[$i]['sort'] = $v['sort'];
                $result[$i]['child'] = $this->getTree($data, $v['id']);
                $i++;
            }
        }
        $result = array_orderby($result, 'sort', SORT_ASC);
        return $result;
    }


}