<?php
namespace common\models;


class Cart extends \yii\db\ActiveRecord
{    
    public $deliveryDate;
    public $deliveryTime;
    
    const SCENARIO_UPDATE = 'update';
    
    public static function tableName() {
        return 'cart';
    }

    public function rules() {
        return[
            [['userId'], 'integer'],
            [['requestedDeliveryOn', 'deliveryDate', 'deliveryTime'], 'safe'],
            ['requestedDeliveryOn', 'validateDeliveryDate', 'on' => self::SCENARIO_UPDATE],
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

}
