<?php

use yii\db\Migration;

class m160517_212928_v0_1_dev_1 extends Migration
{
    public function safeUp()
    {
        $tables = Yii::$app->db->schema->getTableNames();
        $tableOptions = "CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB";

        if (!in_array('epic', $tables)) {
            $this->createTable('{{%description_pack}}', [
                'epic_id' => $this->primaryKey()->unsigned(),
                'key' => $this->string(80)->notNull(),
                'name' => $this->string(80)->notNull(),
                'short' => $this->text()->notNull(),
                'long' => $this->text(),
            ], $tableOptions);
        }
    }

    public function safeDown()
    {
        $this->execute('SET foreign_key_checks = 0;');
        $this->dropTable('{{%epic}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}
