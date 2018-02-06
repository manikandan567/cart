<?php
namespace rest\versions\v1\controllers;

use Yii;
use yii\rest\Controller;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\AccessControl;
use common\models\Cart;
use common\models\CartItem;
use common\models\CartItemDish;
use yii\base\Model;

class CartController extends Controller
{
    
    public function behaviors() {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::className(),
        ];
        $behaviors['access'] = [
            'class' => AccessControl::className(),
            'only' => ['add-to-cart', 'cart-update'],
            'rules' => [
                [
                    'actions' => ['add-to-cart', 'cart-update'],
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
            $currentDish = CartItemDish::find()->where(['dishId' => $cartItemDish->dishId])->one();
            if (!empty($currentDish)) {
                $currentDish->qty = $currentDish->qty + $cartItemDish->qty;
                $currentDish->save();
            } else {
                $cartItem->save();
                $cartItemDish->cartItemId = $cartItem->id;
                $cartItemDish->save();
            }

            Yii::$app->response->statusCode = 201;
            return new \stdClass();
        } else {
            Yii::$app->response->statusCode = 422;
            $response['error']['message'] = current($cartItemDish->getFirstErrors()) ?? null;

            return $response;
        }
    }
    
    public function actionCartUpdate() {
        $cart = Cart::find()->where(['userId' => Yii::$app->user->id])->one();
        $cartItems = $cart->cartItems;
        $bodyParams = Yii::$app->getRequest()->getBodyParams();
        Model::loadMultiple($cartItems, $bodyParams, 'items');
        foreach ($cartItems as $cartItem) {
            $cartItemModel = CartItem::findOne($cartItem->id);
            $cartItemModel->itemDish->qty = $cartItem->qty;
            $cartItemModel->itemDish->save();
        }
    }

}

