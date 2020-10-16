<?php

return [
    'name' => 'doc',
    'identifier' => 'doc.module',// 模块唯一标识[必填]，格式：模块名.[应用市场ID].module.[应用市场分支ID]
    'theme' => 'default',
    'mobile_theme' => '',
    'title' => '文档系统',
    'intro' => 'hi官方出品的文档系统',
    'author' => 'hiphp',
    'icon' => '/static/m_doc/images/app.png',
    'version' => '1.0.0',
    'iconfont' => 'default/css/iconfont.css',
    'author_url' => '',
    'module_depend'=>[],
    'tables'=>['doc','doc_intro','doc_catalog','doc_detail'],
    'language'=>[],
    'db_prefix'=>'pre_',
    'config_icon'=>true,
    'config'=>[]
];