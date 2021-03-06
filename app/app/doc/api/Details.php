<?php declare(strict_types=1);
namespace app\doc\api;
defined('IN_SYSTEM') or die('Access Denied');
use app\common\controller\Common;
class Details extends Common
{

    public function __call($method, $args){
       return json(['code'=>0, 'msg'=>'404']);
    }

	/**
 	* @title 默认
 	* @desc 默认
 	* @url doc/api/details/index
 	* @method RULE
 	* @test 0
 	*/
    public function index(){
        $params = $this->request->param();
        if(!isset($params['iid']) || !is_numeric($params['iid'])){
            return json(['code'=>0, 'msg'=>'param error']);
        }
        $detail = \app\doc\model\DocDetail::where('cid', $params['iid'])->find();
        $result = $detail === null ? ['code'=>0, 'msg'=>'not found'] : ['code'=>1, 'data'=>['id'=>$detail['cid'], 'content'=>$detail['content']]];
        return json($result);
    }



}