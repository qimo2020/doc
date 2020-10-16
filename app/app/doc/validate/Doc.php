<?php declare(strict_types=1);
namespace app\doc\validate;
use think\Validate;
class Doc extends Validate
{
    protected $rule = [
        'title|名称' => 'require|length:3,100|unique:doc',
        'name|标识' => 'require|alphaDash',
    ];
    protected $message = [
        'title.require' => '缺少标题',
        'title.length' => '标题长度不正确',
        'title.unique' => '标题已存在',
        'name.require' => '缺少标识',
    ];

    protected $scene = [
        'edit'  =>  [],
    ];

}
