<?php
namespace common\models;


class CartItemDish extends \yii\db\ActiveRecord
{
    const SCENARIO_ADD_TO_CART = 'add-to-cart';
    
    public static function tableName() {
        return 'cart_item_dish';
    }

    public function rules() {
        return[
            [['cartItemId', 'dishId', 'qty'], 'integer'],
            ['dishId', 'validateDish', 'on' => self::SCENARIO_ADD_TO_CART ]
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
    
    public function validateDish($attributes) {
        $dish = Dish::find()->where(['id' => $this->dishId])->one();
        if (empty($dish)) {
            $this->addError($attributes, 'Invalid Dish');
        }
    }

}
