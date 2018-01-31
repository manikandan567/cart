<?php

return [
    ['class' => 'yii\rest\UrlRule', 'controller' => ['v1/post', 'v1/comment', 'v2/post', 'v3/post']],
    /**
     * User rules
     */
    'OPTIONS v1/users' => 'v1/user/index',
    'GET v1/users' => 'v1/user/index',
    'OPTIONS v1/dish/create' => 'v1/dish/create',
    'POST v1/dish/create' => 'v1/dish/create',
    'OPTIONS v1/dish/delete' => 'v1/dish/delete',
    'DELETE v1/dish/delete' => 'v1/dish/delete',
    'OPTIONS v1/dishes' => 'v1/dish/dishes',
    'GET v1/dishes' => 'v1/dish/dishes',
];

