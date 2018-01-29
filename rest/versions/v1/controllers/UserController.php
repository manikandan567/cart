<?php
namespace rest\versions\v1\controllers;


use yii\rest\Controller;
use common\models\User;
use yii\helpers\ArrayHelper;

/**
 * Class UserController
 * @package rest\versions\v1\controllers
 */
class UserController extends Controller
{
    public function actionIndex()    
    {
        $user = User::find()->all();

		return [
            'data' => ArrayHelper::toArray($user, [
                \common\models\User::class => [
                    'id' => 'id',
                    'name' => 'username',
                    'email' => 'email',
                ]
            ]),
        ];
    }
}
