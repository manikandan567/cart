<?php
namespace common\models;

use Yii;

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Dish extends \yii\db\ActiveRecord
{
    public $actionUserId;

    const SCENARIO_CREATE = 'create';
    const SCENARIO_DELETE = 'delete';
    const SCENARIO_UPDATE = 'update';

    public static function tableName() {
        return 'dish';
    }

    public function rules() {
        return[
            [['name'], 'string', 'max' => '4096'],
            [['price'], 'number'],
            [['name'], 'unique', 'on' => self::SCENARIO_CREATE],
            [['name', 'price'], 'required', 'on' => self::SCENARIO_CREATE],
            [['id'], 'required', 'on' => self::SCENARIO_DELETE],
            ['id', 'validateDish', 'on' => self::SCENARIO_DELETE],
            ['chefId', 'validateChef', 'on' => self::SCENARIO_UPDATE],
        ];
    }

    public function attributeLabels() {
        return[
            'id' => 'Id',
            'name' => 'Name',
            'price' => 'Price',
        ];
    }

    public function validateDish($attributes) {
        $dish = Dish::find()
                ->where(['id' => $this->id])
                ->one();

        if (empty($dish)) {
            $this->addError($attributes, 'Invalid Dish');
        }
    }

    public function validateChef($attributes) {
        if ((int) $this->actionUserId !== (int) $this->chefId) {
            $this->addError($attributes, 'Invalid Chef');
        }
    }

}