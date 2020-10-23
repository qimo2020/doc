<?php declare(strict_types=1);
namespace app\doc\api;
defined('IN_SYSTEM') or die('Access Denied');
use app\common\controller\Common;
class Chapter extends Common
{

    public function __call($method, $args){
       return json(['code'=>0, 'msg'=>'404']);
    }

	/**
 	* @title 默认
 	* @desc 默认
 	* @url doc/api/chapter/index
 	* @method POST
 	* @test 0
 	*/
    public function index(){
        $params = $this->request->param();
        if(!isset($params['member']) || !isset($params['doc'])){
            return json(['code' => 0, 'msg' => $params]);
        }
        $member = \app\doc\model\DocMember::where('name', $params['member'])->find();
        if($member === null){
            return json(['code' => 0, 'msg' => 'error']);
        }
        $doc = \app\doc\model\Doc::where(['name'=>$params['doc'], 'author'=>$member['uid']])->find();
        if($doc === null){
            return json(['code' => 0, 'msg' => 'error']);
        }
        $data = \app\doc\model\DocCatalog::where(['did' => $doc['id']])->select()->toArray();
        $data = getTree($data);
        return json(['code' => 1, 'data' => $data]);
    }

	/**
 	* @title 文档信息
 	* @desc 文档信息
 	* @url doc/api/chapter/info
 	* @method POST
 	* @test 0
 	*/
    public function info(){
        $params = $this->request->param();
        if(!isset($params['member']) || !isset($params['name'])){
            return json(['code' => 0, 'msg' => $params]);
        }
        $member = \app\doc\model\DocMember::where('name', $params['member'])->find();
        if($member === null){
            return json(['code' => 0, 'msg' => 'error2']);
        }
        $doc = \app\doc\model\Doc::where(['name'=>$params['name'], 'author'=>$member['uid']])->find();
        if($doc === null){
            return json(['code' => 0, 'msg' => 'error1']);
        }
        return json(['code' => 1, 'data' => ['title'=>$doc['title'], 'version'=>$doc['version']]]);
    }



}