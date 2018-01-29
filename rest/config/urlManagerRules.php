<?php

return [
		['class' => 'yii\rest\UrlRule', 'controller' => ['v1/post', 'v1/comment', 'v2/post', 'v3/post']],
	/**
	 * User rules
	 */
	'OPTIONS v1/users' => 'v1/user/index',
        'GET v1/users' => 'v1/user/index',
	
];

