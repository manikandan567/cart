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
use yii\web\NotFoundHttpException;
use yii\helpers\ArrayHelper;

class CartController extends Controller
{
    
    public function behaviors() {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::className(),
        ];
        $behaviors['access'] = [
            'class' => AccessControl::className(),
            'only' => ['add-to-cart', 'cart-update', 'cart-item-delete', 'cart-delete', 'my-cart'],
            'rules' => [
                [
                    'actions' => ['add-to-cart', 'cart-update', 'cart-item-delete', 'cart-delete', 'my-cart'],
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
        $cart->load($bodyParams, '');      
        $cart->setScenario(Cart::SCENARIO_UPDATE);
        $date = new \DateTime($cart->deliveryDate);
        $time = new \DateTime($cart->deliveryTime);
        $deliveryDate = new \DateTime($date->format('Y-m-d') . ' ' . $time->format('H:i:s'));
        $cart->requestedDeliveryOn = $deliveryDate->format('Y-m-d H:i:s');
        Model::loadMultiple($cartItems, $bodyParams, 'items');
        if ($cart->validate()) {
            $cart->save();
            foreach ($cartItems as $cartItem) {
                $cartItemModel = CartItem::findOne($cartItem->id);
                $cartItemModel->itemDish->qty = $cartItem->qty;
                $cartItemModel->itemDish->save();
            }
            Yii::$app->response->statusCode = 201;
            return new \stdClass();
        } else {
            Yii::$app->response->statusCode = 422;
            $errors['error']['message'] = current($cart->getFirstErrors()) ?? null;

            return $errors;
        }
    }
    
    public function actionCartItemDelete() {
        $bodyParams = Yii::$app->getRequest()->getBodyParams();
        $cart = Cart::findOne(['userId' => Yii::$app->user->id]);
        $cart->load($bodyParams, '');
        if ($cart->validate()) {
            $cartItem = CartItem::find()
                    ->where(['cartId' => $cart->id, 'id' => $cart->itemId])
                    ->one();
            if (empty($cartItem)) {
                Yii::$app->response->statusCode = 404;
                throw new NotFoundHttpException('The requested page does not exist.');
            } else {
                $cartItem->delete();
                $cartItem->itemDish->delete();
            }
        }
    }
    
    public function actionCartDelete() {
        $user = Yii::$app->user->id;
        $cart = Cart::find()->where(['userId' => $user])->one();
        if (empty($cart)) {
            Yii::$app->response->statusCode = 404;
            throw new NotFoundHttpException('The requested page does not exist.');
        } else {
            $cart->delete();
        }
    }
    
    public function actionMyCart() {
        $cart = Cart::find()->where(['userId' => Yii::$app->user->id])->one();
        if (empty($cart)) {
            Yii::$app->response->statusCode = 404;
            throw new NotFoundHttpException('The requested page does not exist.');
        }
        return ArrayHelper::toArray($cart, [
                    Cart::class => [
                        'id' => 'id',
                        'userId' => 'userId',
                        'deliverydate' => function($cart) {
                            $date = new \DateTime($cart->requestedDeliveryOn);
                            return $date->format('Y-m-d');
                        },
                        'deliveryTime' => function($cart) {
                            $time = new \DateTime($cart->requestedDeliveryOn);
                            return $time->format('H:i');
                        },
                        'dishes' => function($cart) {
                            return $this->getDishDetails($cart);
                        },
                    ]
        ]);
    }

    public function getDishDetails($cart) {
        return ArrayHelper::toArray($cart->cartItems, [
                    CartItem::class => [
                        'id' => 'id',
                        'dishId' => function($item) {
                            return $item->itemDish->dishId;
                        },
                        'dishName' => function($item) {
                            return $item->dish->name;
                        },
                        'dishPrice' => function($item) {
                            return $item->dish->price;
                        },
                    ]
        ]);
    }

}

