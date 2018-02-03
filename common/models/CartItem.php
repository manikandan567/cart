<?php
namespace common\models;


class CartItem extends \yii\db\ActiveRecord
{
    public static function tableName() {
        return 'cart_item';
    }

    public function rules() {
        return[
            [['cartId'], 'integer'],
        ];
    }

    public function attributeLabels() {
        return[
            'id' => 'Id',
            'cartId' => 'CartId',
        ];
    }

}
