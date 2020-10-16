<?php declare(strict_types=1);
namespace app\doc\model;
defined('IN_SYSTEM') or die('Access Denied');
use think\Model;
use think\facade\Db;
class Doc extends Model
{
    public static $error;

    protected static $appTag = 'doc_module';

    public static function getError(){
        return self::$error;
    }

    public function member()
    {
        return $this->hasOne(DocMember::class, 'uid', 'author');
    }

    public function intro()
    {
        return $this->hasOne(DocIntro::class, 'did');
    }

    public static function _save($post){
        Db::startTrans();
        try {
            if(isset($post['id']) && is_numeric($post['id'])){
                $model = self::find($post['id']);
                if($model === null){
                    self::$error = '数据不存在';
                    return false;
                }
                if(isset($post['name'])){
                    unset($post['name']);
                }
                if(!self::update($post)){
                    self::$error = '更新失败';
                    Db::rollback();
                    return false;
                }
                if($post['intro'] && !DocIntro::update(['intro' => $post['intro']], ['did' => $post['id']])){
                    self::$error = '更新介绍失败';
                    Db::rollback();
                    return false;
                }
            }else{
                $model = self::create($post);
                if(!$model || !DocIntro::create(['did'=>$model->id, 'intro'=>$post['intro']])){
                    self::$error = '新增失败';
                    Db::rollback();
                    return false;
                }
            }
            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
            self::$error = $e->getMessage();
            return false;
        }

    }

    public static function remove($id){
        Db::startTrans();
        try {
            self::where('id', $id)->delete();
            DocIntro::where('did', $id)->delete();
            DocDetail::where('did', $id)->delete();
            DocCatalog::where('did', $id)->delete();
            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
            self::$error = $e->getMessage();
            return false;
        }
    }
}