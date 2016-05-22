<?php

use yii\db\Migration;

class m160522_204300_v0_1_dev_2 extends Migration
{
    public function safeUp()
    {
        $tables = Yii::$app->db->schema->getTableNames();
        $tableOptions = "CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB";

        /* Moves dependency from character to person */
        $this->dropForeignKey('character_ibfk_1', '{{%character}}');
        $this->dropColumn('{{%character}}', 'person_id');

        $this->addColumn('{{%person}}', 'character_id',  $this->integer(11)->unsigned());
        $this->addForeignKey('person_ibfk_2', '{{%person}}', 'character_id', '{{%character}}', 'character_id', 'RESTRICT', 'CASCADE');

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
        $this->dropForeignKey('person_ibfk_2', '{{%person}}');
        $this->dropColumn('{{%person}}', 'character_id');

        $this->addColumn('{{%character}}', 'person_id',  $this->integer(11)->unsigned());
        $this->addForeignKey('character_ibfk_1', '{{%character}}', 'person_id', '{{%person}}', 'person_id', 'CASCADE', 'CASCADE');
    }
}
