<?php declare(strict_types=1);
namespace app\doc\api;
defined('IN_SYSTEM') or die('Access Denied');
use app\common\controller\Common;
class Store extends Common
{

    public function __call($method, $args){
       return json(['code'=>0, 'msg'=>'404']);
    }

	/**
 	* @title 默认
 	* @desc 默认
 	* @url doc/api/store/index
 	* @method POST
 	* @test 0
 	*/
    public function index(){
        $member = session('member');
        if (!$member) {
            return json(['code'=>0, 'msg'=>'未登录']);
        }
        $params = $this->request->post();
        $posts = [];
        foreach ($params as $v){
            if(isset($v['book'])){
                $posts['book'] = $v['book'];
            }
            if(isset($v['summary'])){
                $posts['summary'] = $v['summary'];
            }
            if(isset($v['data'])){
                $posts['data'] = $v['data'];
            }
            if(isset($v['sorts'])){
                $posts['sorts'] = $v['sorts'];
            }
            if(isset($v['dels'])){
                $posts['dels'] = $v['dels'];
            }
        }
        if(!isset($posts['book'])){
            return json(['code'=>0, 'msg'=>'param error']);
        }
        \think\facade\Db::startTrans();
        try {
            $books = \app\doc\model\Doc::where(['name'=>$posts['book']])->find();
            if($books === null){
                return json(['code'=>0, 'msg'=>'no doc']);
            }
            $return = ['code'=>1, 'msg'=>'save success'];
            if(isset($posts['dels']) && $posts['dels']){
                $ids = array_column($posts['dels'], 'id');
                $res = \app\doc\model\DocCatalog::destroy($ids);
                $res = \app\doc\model\DocDetail::where('cid','in', $ids)->delete();
            }
            if(isset($posts['summary'])){
                $data = \app\doc\model\DocCatalog::where('did', $books['id'])->select()->toArray();
                $files = array_column($data, 'file', 'id');
                $titles = array_column($data, 'title', 'id');
                $res = catalogTree($books['id'], $posts['summary'], $data, $files, $titles);
                $return['files'] = $files;
                if(isset($posts['sorts'])){
                    $data = \app\doc\model\DocCatalog::where('did', $books['id'])->select()->toArray();
                    $sorts = array_column($posts['sorts'], 'id');
                    $newSorts = sortReset($posts['summary'], $sorts, $data);
                    (new \app\doc\model\DocCatalog())->saveAll($newSorts);
                }
                $newData = \app\doc\model\DocCatalog::where('did', $books['id'])->select()->toArray();
                $return['summary'] = getTree($newData);
            }
            if(isset($posts['data'])){
                $details = [];
                $detailUpdates = [];
                $authDetail = \app\doc\model\DocDetail::where('did', $books['id'])->select()->toArray();
                $ids = array_column($authDetail, 'cid');
                foreach($posts['data'] as $v){
                    $details[] = ['name'=>$v['name']];
                    if(isset($v['id']) && in_array($v['id'], $ids)){
                        \app\doc\model\DocDetail::update(['content'=>$v['content']], ['cid' => $v['id']]);
                    }
                }
                if($details) $return['data'] = $ids;
            }
            \think\facade\Db::commit();
            return json($return);
        } catch (\Exception $e) {
            \think\facade\Db::rollback();
            return json(['code'=>0, 'msg'=>$e->getMessage().'['.$e->getFile().$e->getLine().']']);
        }
    }

	/**
 	* @title 图片上传
 	* @desc 图片上传
 	* @url doc/api/store/upload
 	* @method POST
 	* @test 0
 	*/
    public function upload($from = 'input', $group = 'doc', $water = '', $thumb = '', $thumb_type = '', $input = 'file'){
        $member = session('member');
        if (!$member) {
            return json(['code'=>0, 'msg'=>'未登录']);
        }
        $result = \app\common\model\SystemAnnex::upload($from, $group, $water, $thumb, $thumb_type, $input);
        return json(['code'=>0, 'msg'=>'doc/api/store/upload', 'file_path'=>$result['data']['file']]);
    }



}