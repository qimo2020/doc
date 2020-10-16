<?php declare(strict_types=1);
namespace app\doc\home;
defined('IN_SYSTEM') or die('Access Denied');
use app\common\controller\Common;
use app\system\model\SystemModule as ModuleModel;
use think\facade\Request;
class Base extends Common
{
    protected function initialize()
    {
        parent::initialize();
        configs('doc');
        $default = ModuleModel::getDefaultModule();
        $query = strtolower($this->request->query());
        $docBase = strpos($query, 'doc') === false && $default && $default['name'] == 'doc' ? '' : 'doc/';
        define('DOC_BASE', $docBase);
    }

}
