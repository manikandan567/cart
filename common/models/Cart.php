<?php
namespace common\models;


class Cart extends \yii\db\ActiveRecord
{    
    public static function tableName() {
        return 'cart';
    }

    public function rules() {
        return[
            [['userId'], 'integer'],
            [['requestedDeliveryOn'], 'safe'],
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

}
