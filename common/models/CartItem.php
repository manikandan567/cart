<?php
namespace common\models;


class CartItem extends \yii\db\ActiveRecord
{
    public $qty;
    
    public static function tableName() {
        return 'cart_item';
    }

    public function rules() {
        return[
            [['cartId', 'qty'], 'integer'],
            [['qty', 'id'], 'required'],
        ];
    }

    public function attributeLabels() {
        return[
            'id' => 'Id',
            'cartId' => 'CartId',
        ];
    }
    
    public function getItemDish() {
        return $this->hasOne(CartItemDish::className(), ['cartItemId' => 'id']);
    }

}
