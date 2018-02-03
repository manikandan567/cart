<?php

use yii\db\Migration;

/**
 * Class m180203_054729_cart
 */
class m180203_054729_cart extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('cart', [
            'id' => $this->primaryKey()->unsigned(),
            'userId' => $this->integer(11)->null(),
            'requestedDeliveryOn' => $this->timestamp()->null(),
        ],'ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci');

    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m180203_054729_cart cannot be reverted.\n";

        return false;
    }
}
