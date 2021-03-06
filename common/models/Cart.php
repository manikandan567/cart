<?php
namespace common\models;

use Yii;
use DateInterval;

class Cart extends \yii\db\ActiveRecord
{
    public $deliveryDate;
    public $deliveryTime;
    public $items;
    public $itemId;

    const SCENARIO_UPDATE = 'update';

    public static function tableName() {
        return 'cart';
    }

    public function rules() {
        return[
            [['userId'], 'integer'],
            [['requestedDeliveryOn', 'deliveryDate', 'deliveryTime', 'items', 'itemId'], 'safe'],
            ['requestedDeliveryOn', 'validatePastDeliveryDate', 'on' => self::SCENARIO_UPDATE],
            ['requestedDeliveryOn', 'validatePossibleDeliveryDate', 'on' => self::SCENARIO_UPDATE],
            ['items', 'validateCartItems', 'on' => self::SCENARIO_UPDATE],
        ];
    }

    public function attributeLabels() {
        return[
            'id' => 'Id',
            'userId' => 'UserId',
            'requestedDeliveryOn' => 'Requested Delivery On',
        ];
    }
    
    public function getCartItem() {
        return $this->hasOne(CartItem::className(), ['cartId' => 'id']);
    }

    public function getCartItems() {
        return $this->hasMany(CartItem::className(), ['cartId' => 'id']);
    }
    
    public function getCartItemDish() {
        return $this->hasOne(CartItemDish::className(), ['cartItemId' => 'id'])
                        ->via('cartItem');
    }
    
    public function getCartItemDishes() {
        return $this->hasMany(CartItemDish::className(), ['cartItemId' => 'id'])
                        ->via('cartItems');
    }
    
    public function getDish() {
        return $this->hasOne(Dish::className(), ['id' => 'dishId'])
                        ->via('cartItemDish');
    }

    public function getDishes() {
        return $this->hasMany(Dish::className(), ['id' => 'dishId'])
                        ->via('cartItemDishes');
    }

    public function validatePastDeliveryDate($attributes) {
        $currentDate = new \DateTime();
        $deliveryDate = new \DateTime($this->requestedDeliveryOn);
        if ($deliveryDate->format('Y-m-d H:i:s') < $currentDate->format('Y-m-d H:i:s')) {
            $this->addError($attributes, 'Delivery Date Cannot be Past Date');
        }
    }

    public function validatePossibleDeliveryDate($attributes) {
        $currentDate = new \DateTime();
        $possibleDeliveryDate = $currentDate->add(new DateInterval('P1M'));
        $deliveryDate = new \DateTime($this->requestedDeliveryOn);
        if ($deliveryDate->format('Y-m-d H:i:s') > $possibleDeliveryDate->format('Y-m-d H:i:s')) {
            $this->addError($attributes, 'Delivery Date should be within 1 month from today, max.possible delivery date is ' . $possibleDeliveryDate->format('Y-m-d'));
        }
    }

    public function validateCartItems($attributes) {
        $cartItemIds1 = [];
        $cartItemIds2 = [];
        foreach ($this->items as $cartItem) {
            if (empty($cartItem['id'])) {
                $this->addError($attributes, 'Invalid item');
            } else {
                $cartItemIds1[] = $cartItem['id'];
            }
        }
        $cartItems = CartItem::find()->andWhere(['cartId' => $this->id])->all();
        foreach ($cartItems as $item) {
            $cartItemIds2[] = $item['id'];
        }
        if (!empty(array_diff($cartItemIds1, $cartItemIds2))) {
            $this->addError($attributes, 'Invalid item');
        }
    }
    
    public function beforeDelete() {
        foreach ($this->cartItemDishes as $cartItemDish) {
            $cartItemDish->delete();
        }
        foreach ($this->cartItems as $cartItem) {
            $cartItem->delete();
        }
        return parent::beforeDelete();
    }

}
