<?php declare(strict_types=1);
namespace app\doc\validate;
use think\Validate;
class DocMember extends Validate
{
    protected $rule = [
        'name|用户名' => 'require|alphaDash|length:3,60|unique:doc_member',
    ];
    protected $message = [
        'name.require' => '用户名标题',
        'name.length' => '用户名长度为3-60位',
        'name.unique' => '用户名已存在',
        'name.alphaDash' => '存在非法字符',
    ];

}
