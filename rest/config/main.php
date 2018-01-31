<?php

$params = array_merge(
    require(__DIR__ . '/params.php')
);

return [
    'id' => 'rest-api',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'modules' => [
        'v1' => [
            'class' => 'rest\versions\v1\RestModule'
        ],
    ],
    'components' => [
        'user' => [
            'identityClass' => 'common\models\User',
            'enableSession' => false,
        ],
        'response' => [
            'format' => yii\web\Response::FORMAT_JSON,
            'charset' => 'UTF-8',
            'as beforeSend' => 'rest\behaviors\ResponseBeforeSendBehavior',
        ],
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'request' => [
            'class' => '\yii\web\Request',
            'enableCookieValidation' => false,
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ],
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'enableStrictParsing' => true,
            'showScriptName' => false,
            'rules' => [
                ['class' => 'yii\rest\UrlRule', 'controller' => ['v1/post', 'v1/comment', 'v2/post']],
                'OPTIONS v1/users' => 'v1/user/index',
                'GET v1/users' => 'v1/user/index',
                'OPTIONS v1/dish/create' => 'v1/dish/create',
                'POST v1/dish/create' => 'v1/dish/create',
                'OPTIONS v1/dishes' => 'v1/dish/dishes',
                'GET v1/dishes' => 'v1/dish/dishes',
                'OPTIONS v1/dish/delete' => 'v1/dish/delete',
                'DELETE v1/dish/delete' => 'v1/dish/delete',
            ],
        ],
    ],
    'params' => $params,
];
