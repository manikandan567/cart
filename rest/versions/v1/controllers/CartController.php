<?php
namespace rest\versions\v1\controllers;

use Yii;
use yii\rest\Controller;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\AccessControl;
use common\models\Cart;
use common\models\CartItem;
use common\models\CartItemDish;

class CartController extends Controller
{
    public function behaviors() {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::className(),
        ];
        $behaviors['access'] = [
            'class' => AccessControl::className(),
            'only' => ['add-to-cart'],
            'rules' => [
                [
                    'actions' => ['add-to-cart'],
                    'allow' => true,
                    'roles' => ['@'],
                ],
            ],
        ];
        return $behaviors;
    }

    public function actionAddToCart() {
        $bodyParams = Yii::$app->getRequest()->getBodyParams();
        $user = Yii::$app->user->id;
        $cart = Cart::find()->where(['userId' => $user])->one();
        if (empty($cart)) {
            $cart = new Cart();
            $cart->userId = $user;
            $cart->save();
        }

        $cartItem = new CartItem();
        $cartItem->cartId = $cart->id;

        $cartItemDish = new CartItemDish(['scenario' => CartItemDish::SCENARIO_ADD_TO_CART]);
        $cartItemDish->load($bodyParams, '');
        if ($cartItemDish->validate()) {
            $cartItem->save();
            $cartItemDish->cartItemId = $cartItem->id;
            $cartItemDish->dishId = $bodyParams['dishId'];
            $cartItemDish->qty = $bodyParams['qty'];
            $cartItemDish->save();
        } else {
            Yii::$app->response->statusCode = 422;
            $response['error']['message'] = current($cartItemDish->getFirstErrors()) ?? null;

            return $response;
        }
    }

}

