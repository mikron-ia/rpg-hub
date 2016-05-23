<?php

use yii\db\Migration;

class m160522_204300_v0_1_dev_membership extends Migration
{
    public function safeUp()
    {
        $tables = Yii::$app->db->schema->getTableNames();
        $tableOptions = "CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB";

        /* Adds dependency between person and group */
        if (!in_array('membership', $tables)) {
            $this->createTable('{{%membership}}', [
                'membership_id' => $this->primaryKey()->unsigned(),
                'person_id' => $this->integer(11)->unsigned()->notNull(),
                'group_id' => $this->integer(11)->unsigned()->notNull(),
                'key' => $this->string(80)->notNull(),
            ], $tableOptions);
        }
    }

    public function safeDown()
    {
        $this->dropTable('membership');
    }
}
