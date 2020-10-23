<?php declare(strict_types=1);
namespace app\doc\api;
defined('IN_SYSTEM') or die('Access Denied');
use app\common\controller\Common;
class Catalog extends Common
{

    public function __call($method, $args){
       return json(['code'=>0, 'msg'=>'404']);
    }

	/**
 	* @title 默认
 	* @desc 默认
 	* @url doc/api/catalog/index
 	* @method GET
 	* @test 0
 	*/
    public function index(){
        $member = session('member');
        if (!$member) {
            return json(['code'=>0, 'msg'=>'未登录']);
        }
        $params = $this->request->param();
        $books = \app\doc\model\Doc::where('name', $params['book'])->find();
        if($books === null){
            return json(['code' => 0, 'msg' => '不存在']);
        }
        $data = \app\doc\model\DocCatalog::where(['did' => $books['id']])->select()->toArray();
        $data = getTree($data);
        return json(['code' => 1, 'data' => $data]);
    }



}