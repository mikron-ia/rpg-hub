<?php

use yii\db\Migration;

class m160517_212928_v0_1_dev_1 extends Migration
{
    public function safeUp()
    {
        $tables = Yii::$app->db->schema->getTableNames();
        $tableOptions = "CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB";

        if (!in_array('epic', $tables)) {
            $this->createTable('{{%epic}}', [
                'epic_id' => $this->primaryKey()->unsigned(),
                'key' => $this->string(80)->notNull(),
                'name' => $this->string(80)->notNull()->comment('Public name for the epic'),
                'system' => $this->string(20)->notNull()->comment('Code for the system used'),
            ], $tableOptions);
        }

        $this->addColumn('{{%story}}', 'epic_id', $this->integer(11)->unsigned()->notNull() . ' AFTER story_id');
        $this->addColumn('{{%recap}}', 'epic_id', $this->integer(11)->unsigned()->notNull() . ' AFTER recap_id');
        $this->addColumn('{{%group}}', 'epic_id', $this->integer(11)->unsigned()->notNull() . ' AFTER group_id');
        $this->addColumn('{{%person}}', 'epic_id', $this->integer(11)->unsigned()->notNull() . ' AFTER person_id');
        $this->addColumn('{{%character}}', 'epic_id', $this->integer(11)->unsigned()->notNull() . ' AFTER character_id');

        $this->addForeignKey('story_ibfk_1', '{{%story}}', 'epic_id', '{{%epic}}', 'epic_id', 'RESTRICT', 'CASCADE');
        $this->addForeignKey('recap_ibfk_1', '{{%recap}}', 'epic_id', '{{%epic}}', 'epic_id', 'RESTRICT', 'CASCADE');
        $this->addForeignKey('group_ibfk_1', '{{%group}}', 'epic_id', '{{%epic}}', 'epic_id', 'RESTRICT', 'CASCADE');
        $this->addForeignKey('person_ibfk_1', '{{%person}}', 'epic_id', '{{%epic}}', 'epic_id', 'RESTRICT', 'CASCADE');
        $this->addForeignKey('character_ibfk_2', '{{%character}}', 'epic_id', '{{%epic}}', 'epic_id', 'RESTRICT', 'CASCADE');
    }

    public function safeDown()
    {
        $this->execute('SET foreign_key_checks = 0;');
        $this->dropForeignKey('story_ibfk_1', '{{%story}}');
        $this->dropForeignKey('recap_ibfk_1', '{{%recap}}');
        $this->dropForeignKey('group_ibfk_1', '{{%group}}');
        $this->dropForeignKey('person_ibfk_1', '{{%person}}');
        $this->dropForeignKey('character_ibfk_2', '{{%character}}');
        $this->dropColumn('{{%story}}', 'epic_id');
        $this->dropColumn('{{%recap}}', 'epic_id');
        $this->dropColumn('{{%group}}', 'epic_id');
        $this->dropColumn('{{%person}}', 'epic_id');
        $this->dropColumn('{{%character}}', 'epic_id');
        $this->dropTable('{{%epic}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}
