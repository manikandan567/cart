<?php
namespace common\models;


class Cart extends \yii\db\ActiveRecord
{
    public $deliveryDate;
    public $deliveryTime;
    public $items;

    const SCENARIO_UPDATE = 'update';

    public static function tableName() {
        return 'cart';
    }

    public function rules() {
        return[
            [['userId'], 'integer'],
            [['requestedDeliveryOn', 'deliveryDate', 'deliveryTime', 'items'], 'safe'],
            ['requestedDeliveryOn', 'validateDeliveryDate', 'on' => self::SCENARIO_UPDATE],
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

    public function getCartItems() {
        return $this->hasMany(CartItem::className(), ['cartId' => 'id']);
    }

    public function validateDeliveryDate($attributes) {
        $currentDate = new \DateTime();
        $deliveryDate = new \DateTime($this->requestedDeliveryOn);
        if ($deliveryDate->format('Y-m-d H:i:s') < $currentDate->format('Y-m-d H:i:s')) {
            $this->addError($attributes, 'Delivery Date Cannot be Past Date');
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

}
