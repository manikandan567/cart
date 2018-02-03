<?php

use yii\db\Migration;

/**
 * Class m180203_075716_cart_item_dish
 */
class m180203_075716_cart_item_dish extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('cart_item_dish', [
            'id' => $this->primaryKey()->unsigned(),
            'cartItemId' => $this->integer(11)->null(),
            'dishId' => $this->integer(11)->null(),
            'qty' => $this->integer(11)->null(),
        ],'ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci');

    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m180203_075716_cart_item_dish cannot be reverted.\n";

        return false;
    }
}
