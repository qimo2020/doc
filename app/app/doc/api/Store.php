<?php declare(strict_types=1);
namespace app\doc\api;
defined('IN_SYSTEM') or die('Access Denied');
use app\common\controller\Common;
use app\common\model\SystemAnnex as AnnexModel;
use app\doc\model\Doc;
use app\doc\model\DocCatalog;
use app\doc\model\DocDetail;
use think\facade\Db;
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
//        $member = session('member');
//        if (!$member) {
//            return json(['code'=>0, 'msg'=>'未登录']);
//        }
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
        Db::startTrans();
        try {
            $books = Doc::where(['name'=>$posts['book']])->find();
            if($books === null){
                return json(['code'=>0, 'msg'=>'no doc']);
            }
            $return = ['code'=>1, 'msg'=>'save success'];
            if(isset($posts['dels']) && $posts['dels']){
                $ids = array_column($posts['dels'], 'id');
                $res = DocCatalog::destroy($ids);
                $res = DocDetail::where('cid','in', $ids)->delete();
            }
            if(isset($posts['summary'])){
                $data = DocCatalog::where('did', $books['id'])->select()->toArray();
                $files = array_column($data, 'file', 'id');
                $titles = array_column($data, 'title', 'id');
                $res = $this->catalogTree($books['id'], $posts['summary'], $data, $files, $titles);
                $return['files'] = $files;
                if(isset($posts['sorts'])){
                    $data = DocCatalog::where('did', $books['id'])->select()->toArray();
                    $sorts = array_column($posts['sorts'], 'id');
                    $newSorts = $this->sortReset($posts['summary'], $sorts, $data);
                    (new DocCatalog())->saveAll($newSorts);
                }
                $newData = DocCatalog::where('did', $books['id'])->select()->toArray();
                $return['summary'] = $this->getTree($newData);
            }
            if(isset($posts['data'])){
                $details = [];
                $detailUpdates = [];
                $authDetail = DocDetail::where('did', $books['id'])->select()->toArray();
                $ids = array_column($authDetail, 'cid');
                foreach($posts['data'] as $v){
                    $details[] = ['name'=>$v['name']];
                    if(isset($v['id']) && in_array($v['id'], $ids)){
                        DocDetail::update(['content'=>$v['content']], ['cid' => $v['id']]);
                    }
                }
                if($details) $return['data'] = $ids;
            }
            Db::commit();
            return json($return);
        } catch (\Exception $e) {
            Db::rollback();
            return json(['code'=>0, 'msg'=>$e->getMessage().'['.$e->getFile().$e->getLine().']']);
        }

    }

    private function sortReset($summary, $sorts, $data, $level=0){
        $result = [];
        if(0 === $level && in_array(0, $sorts)){
            foreach ($summary as $k=>$v){
                if(isset($v['id'])){
                    $result[] = ['sort'=>$k, 'id'=>$v['id']];
                }
            }
        }
        $diffs = $sorts;
        if($key = array_search(0, $diffs)){
            unset($diffs[$key]);
        }
        if(count($diffs) > 0){
            foreach ($summary as $k=>$v){
                if(isset($v['id'])){
                    if(in_array($v['id'], $sorts) && $v['child']){
                        foreach ($v['child'] as $key=>$val){
                            if(isset($val['id'])){
                                $result[] = ['sort'=>$key, 'id'=>$val['id']];
                            }
                        }
                    }
                    if($v['child']){
                        $level++;
                        $this->sortReset($v['child'], $sorts, $data, $level);
                    }
                }
            }
        }
        return $result;
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
    private function catalogTree($doc, $array, $data, $files, $titles, $pid=0){
        $addId = null;
        foreach ($array as $v){
            if(isset($v['id']) && array_key_exists($v['id'], $files)){
                $addId = $v['id'];
                if($v['name'] != $titles[$v['id']] || $v['file'] != $files[$v['id']]){
                    $res = DocCatalog::update(['title'=>$v['name'], 'file'=>$v['file'], 'sort'=>$v['sort']], ['id'=>$v['id']]);
                }
            }else if(!isset($v['id'])){
                $res = DocCatalog::create(['did'=>$doc, 'pid'=>$pid, 'title'=>$v['name'], 'file'=>$v['file'], 'sort'=>$v['sort']]);
                $addId = $res->id;
                $res = DocDetail::create(['did'=>$doc, 'cid'=>$res->id]);
            }
            if($v['child']){
                $this->catalogTree($doc, $v['child'], $data, $files, $titles, $addId);
            }
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
        $result = AnnexModel::upload($from, $group, $water, $thumb, $thumb_type, $input);
        return json(['code'=>0, 'msg'=>'doc/api/store/upload', 'file_path'=>$result['data']['file']]);
    }



}