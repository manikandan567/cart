<?php

use yii\db\Migration;

/**
 * Class m180203_074735_cart_item
 */
class m180203_074735_cart_item extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('cart_item', [
            'id' => $this->primaryKey()->unsigned(),
            'cartId' => $this->integer(11)->null(),
        ],'ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci');

    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m180203_074735_cart_item cannot be reverted.\n";

        return false;
    }
}
