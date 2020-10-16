<?php declare(strict_types=1);
namespace app\doc\home;
defined('IN_SYSTEM') or die('Access Denied');
use app\doc\model\Doc;
use app\doc\model\DocMember;
use app\doc\validate\Doc as DocValidate;
use app\doc\validate\DocMember as DocMemberValidate;
use think\exception\HttpResponseException;
use think\exception\ValidateException;
use think\exception\HttpException;
class Author extends Base
{
    protected $member;

    public function __call($method, $args)
    {
        throw new HttpException(404);
    }

    protected function initialize()
    {
        parent::initialize();
        $member = session('member');
        if (!$member) {
            throw new HttpResponseException($this->response(0, '未登录', '/doc'));
        }
        $this->member = $member;
    }

    public function index(){
        $docs = Doc::where('author', $this->member['uid'])->withJoin(['member','intro'], 'left')->select();
        $this->assign('docs', $docs);

        return $this->view();
    }

    public function add(){
        if($this->request->post()){
            $post = $this->request->post();
            try {
                validate(DocValidate::class)->check($post);
            } catch (ValidateException $e) {
                return $this->response(0, $e->getError());
            }
            if(null === DocMember::where('uid', $this->member['uid'])->find()){
                return $this->response(0, '请先创建用户名', '');
            }
            $post['author'] = $this->member['uid'];
            if(Doc::_save($post)){
                return $this->response(1, 'success', '', $post);
            }else{
                return $this->response(0, Doc::getError());
            }

        }
        return $this->view('store');
    }

    public function edit(){
        if($this->request->post()){
            $post = $this->request->post();
            try {
                validate(DocValidate::class)->scene('edit')->check($post);
            } catch (ValidateException $e) {
                return $this->response(0, $e->getError());
            }
            if(Doc::_save($post)){
                return $this->response(1, 'success');
            }else{
                return $this->response(0, Doc::getError());
            }
        }
        $gets = $this->request->get();
        if(!$gets || !isset($gets['id'])){
            return $this->response(0, '非法请求');
        }
        $docs = Doc::where(['author'=>$this->member['uid']])->withJoin('intro', 'left')->find($gets['id']);
        $this->assign('docs', $docs);
        return $this->view('store');
    }

    public function account(){
        if($this->request->post()){
            $post = $this->request->post();
            try {
                validate(DocMemberValidate::class)->check($post);
            } catch (ValidateException $e) {
                return $this->response(0, $e->getError());
            }
            $post['uid'] = $this->member['uid'];
            if(DocMember::_save($post)){
                return $this->response(1, 'success');
            }else{
                return $this->response(0, Doc::getError());
            }
        }
        if(null === $member = DocMember::where('uid', $this->member['uid'])->find()){
            $member = [];
        }
        $this->assign('member', $member);
        return $this->view();
    }

    public function update(){
        if($this->request->post()){
            $post = $this->request->post();
            if(Doc::where('id', $post['id'])->inc('version')->update()){
                return $this->response(1, '已更新', '', $post);
            }else{
                return $this->response(0, Doc::getError());
            }
        }
    }

    public function remove(){
        if(!$this->request->isPost()){
            return $this->response(0, '非法请求');
        }
        $post = $this->request->post();
        if(!Doc::remove($post['id'])){
            return $this->response(0, Doc::getError());
        }
        return $this->response(1, '已删除');
    }

}
