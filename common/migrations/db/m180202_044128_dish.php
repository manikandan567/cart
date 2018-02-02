<?php

use yii\db\Migration;

/**
 * Class m180202_044128_dish
 */
class m180202_044128_dish extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('dish', [
            'id' => $this->primaryKey()->unsigned(),
            'chefId' => $this->integer(11)->null(),
            'name' => 'varchar(4096) NULL',
            'price' => 'FLOAT(10,2) NULL',
        ], 'ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci');
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m180202_044128_dish cannot be reverted.\n";

        return false;
    }
}
