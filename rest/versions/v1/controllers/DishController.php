<?php
namespace rest\versions\v1\controllers;

use Yii;
use yii\rest\Controller;
use common\models\Dish;
use yii\helpers\ArrayHelper;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\AccessControl;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class DishController extends Controller
{
    public function behaviors() {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => CompositeAuth::className(), 
            'optional' => ['create'],
            'authMethods' => [
                HttpBearerAuth::className(),
            ],
        ];
        $behaviors['access']	    = [
            'class' => AccessControl::className(),
            'only' => [],
            'rules' => [
                    [
                        'actions' => ['create'],
                            'allow' => true,
                            'roles' => ['chef'],
                    ],
            ],
        ];
        return $behaviors;
    }

        public function actionCreate()
    {
        $dish = new Dish();
        $bodyParams = Yii::$app->getRequest()->getBodyParams();
        $dish->load($bodyParams, '');
        $dish->save();
    }
    
    public function actionDishes()
    {
        $dish = Dish::find()->all();

            return [
            'data' => ArrayHelper::toArray($dish, [
                Dish::class => [
                    'id' => 'id',
                    'name' => 'name',
                    'price' => 'price',
                ]
            ]),
        ];
    }
}