<?php declare(strict_types=1);
namespace app\doc\api;
defined('IN_SYSTEM') or die('Access Denied');
use app\common\controller\Common;
use app\doc\model\Doc;
use app\doc\model\DocCatalog;
use app\doc\model\DocMember;

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
        $member = DocMember::where('name', $params['member'])->find();
        if($member === null){
            return json(['code' => 0, 'msg' => 'error']);
        }
        $doc = Doc::where(['name'=>$params['doc'], 'author'=>$member['uid']])->find();
        if($doc === null){
            return json(['code' => 0, 'msg' => 'error']);
        }
        $data = DocCatalog::where(['did' => $doc['id']])->select()->toArray();
        $data = $this->getTree($data);
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
        $member = DocMember::where('name', $params['member'])->find();
        if($member === null){
            return json(['code' => 0, 'msg' => 'error2']);
        }
        $doc = Doc::where(['name'=>$params['name'], 'author'=>$member['uid']])->find();
        if($doc === null){
            return json(['code' => 0, 'msg' => 'error1']);
        }
        return json(['code' => 1, 'data' => ['title'=>$doc['title'], 'version'=>$doc['version']]]);
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