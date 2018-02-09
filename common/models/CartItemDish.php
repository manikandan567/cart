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
            [['qty'], 'required'],
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
    
    public function getDish() {
        return $this->hasOne(Dish::className(), ['id' => 'dishId']);
    }

    public function getDishes() {
        return $this->hasMany(Dish::className(), ['id' => 'dishId']);
    }

    public function validateDish($attributes) {
        $dish = Dish::find()->where(['id' => $this->dishId])->one();
        if (empty($dish)) {
            $this->addError($attributes, 'Invalid Dish');
        }
    }

}
