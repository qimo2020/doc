<?php
return [
'alias'=>[
'ApiAuth'=>\app\api\middleware\ApiAuth::class,
'UserAuth'=>\app\api\middleware\UserAuth::class,
'ParamCheck'=>\app\api\middleware\ParamCheck::class,
],
'priority'=>[
],
];