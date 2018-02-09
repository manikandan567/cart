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
            [['id'], 'safe'],
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
    
    public function getDish() {
        return $this->hasOne(Dish::className(), ['id' => 'dishId'])
                        ->via('itemDish');
    }

}
