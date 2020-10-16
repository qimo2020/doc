<?php declare(strict_types=1);
namespace app\doc\model;
defined('IN_SYSTEM') or die('Access Denied');

use think\facade\Db;
use think\Model;
class DocMember extends Model
{
    public static $error;
    public static function getError(){
        return self::$error;
    }

    public static function _save($post){
        Db::startTrans();
        try {
            if(isset($post['id']) && is_numeric($post['id'])){
                //TODO
            }else{
                if(!self::create(['uid'=>$post['uid'], 'name'=>$post['name']])){
                    self::$error = '创建失败';
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

}