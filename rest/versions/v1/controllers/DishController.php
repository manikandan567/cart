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
            'optional' => ['create', 'delete'],
            'authMethods' => [
                HttpBearerAuth::className(),
            ],
        ];
        $behaviors['access'] = [
            'class' => AccessControl::className(),
            'only' => [],
            'rules' => [
                [
                    'actions' => ['create', 'delete'],
                    'allow' => true,
                    'roles' => ['chef'],
                ],
            ],
        ];
        return $behaviors;
    }

    public function actionCreate() {
        $dish = new Dish(['scenario' => Dish::SCENARIO_CREATE]);
        $bodyParams = Yii::$app->getRequest()->getBodyParams();
        $user = Yii::$app->user->id;
        $dish->load($bodyParams, '');
        if ($dish->validate()) {
            $dish->chefId = $user;
            $dish->save();
            Yii::$app->response->statusCode = 201;
            return [
                'dishId' => $dish->id,
            ];
        } else {
            Yii::$app->response->statusCode = 422;
            $response['error']['message'] = current($dish->getFirstErrors()) ?? null;

            return $response;
        }
    }

    public function actionDelete() {
        $dish = new Dish(['scenario' => Dish::SCENARIO_DELETE]);
        $bodyParams = Yii::$app->getRequest()->getBodyParams();
        if ($dish->load($bodyParams, '') && $dish->validate()) {
            $dish = Dish::findOne($bodyParams['id']);
            $dish->delete();
        } else {
            Yii::$app->response->statusCode = 422;
            $response['error']['message'] = current($dish->getFirstErrors()) ?? null;

            return $response;
        }
    }

    public function actionDishes() {
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