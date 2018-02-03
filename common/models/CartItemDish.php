<?php
namespace common\models;


class CartItemDish extends \yii\db\ActiveRecord
{
    public static function tableName() {
        return 'cart_item_dish';
    }

    public function rules() {
        return[
            [['cartItemId', 'dishId', 'qty'], 'integer'],
        ];
    }

    public function attributeLabels() {
        return[
            'id' => 'Id',
            'cartItemId' => 'CartItemId',
            'dishId' => 'DishId',
            'qty' => 'Qty',
        ];
    }

}
