<?php
use think\facade\Request;
use think\facade\Route;

$pathInfo = Request::instance()->pathinfo();
Route::rule('write/:name', 'write/:name');
