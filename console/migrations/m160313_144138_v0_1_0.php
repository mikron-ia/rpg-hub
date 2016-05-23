<?php

use yii\db\Migration;

class m160313_144138_v0_1_0 extends Migration
{
    public function safeUp()
    {
        $tables = Yii::$app->db->schema->getTableNames();
        $tableOptions = "CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB";

        if (!in_array('character', $tables)) {
            $this->createTable('{{%character}}', [
                'character_id' => $this->primaryKey()->unsigned(),
                'epic_id' => $this->integer(11)->unsigned()->notNull(),
                'key' => $this->string(80)->notNull(),
                'name' => $this->string(120)->notNull(),
                'data' => $this->text()->notNull(),
            ], $tableOptions);
        }

        if (!in_array('description', $tables)) {
            $this->createTable('{{%description}}', [
                'description_id' => $this->primaryKey()->unsigned(),
                'description_pack_id' => $this->integer(11)->unsigned(),
                'title' => $this->string(80)->notNull(),
                'code' => $this->string(40)->notNull(),
                'public_text' => $this->text()->notNull(),
                'private_text' => $this->text()->notNull(),
                'lang' => $this->string(8)->notNull(),
            ], $tableOptions);
        }

        if (!in_array('description_pack', $tables)) {
            $this->createTable('{{%description_pack}}', [
                'description_pack_id' => $this->primaryKey()->unsigned(),
                'name' => $this->string(80)->notNull(),
            ], $tableOptions);
        }

        if (!in_array('epic', $tables)) {
            $this->createTable('{{%epic}}', [
                'epic_id' => $this->primaryKey()->unsigned(),
                'key' => $this->string(80)->notNull(),
                'name' => $this->string(80)->notNull()->comment('Public name for the epic'),
                'system' => $this->string(20)->notNull()->comment('Code for the system used'),
            ], $tableOptions);
        }

        if (!in_array('group', $tables)) {
            $this->createTable('{{%group}}', [
                'group_id' => $this->primaryKey()->unsigned(),
                'epic_id' => $this->integer(11)->unsigned()->notNull(),
                'key' => $this->string(80)->notNull(),
                'name' => $this->string(120)->notNull(),
                'data' => $this->text()->notNull(),
            ], $tableOptions);
        }

        if (!in_array('person', $tables)) {
            $this->createTable('{{%person}}', [
                'person_id' => $this->primaryKey()->unsigned(),
                'epic_id' => $this->integer(11)->unsigned()->notNull(),
                'key' => $this->string(80)->notNull(),
                'name' => $this->string(120)->notNull(),
                'tagline' => $this->string(120)->notNull(),
                'data' => $this->text()->notNull(),
                'visibility' => 'ENUM(\'none\',\'linked\',\'complete\') NULL',
                'character_id' => $this->integer(11)->unsigned()
            ], $tableOptions);
        }

        if (!in_array('recap', $tables)) {
            $this->createTable('{{%recap}}', [
                'recap_id' => $this->primaryKey()->unsigned(),
                'epic_id' => $this->integer(11)->unsigned()->notNull(),
                'key' => $this->string(80)->notNull(),
                'name' => $this->string(120)->notNull(),
                'data' => $this->text()->notNull(),
                'time' => $this->dateTime()->notNull(),
            ], $tableOptions);
        }

        if (!in_array('story', $tables)) {
            $this->createTable('{{%story}}', [
                'story_id' => $this->primaryKey()->unsigned(),
                'epic_id' => $this->integer(11)->unsigned()->notNull(),
                'key' => $this->string(80)->notNull(),
                'name' => $this->string(120)->notNull(),
                'short' => $this->text()->notNull(),
                'long' => $this->text(),
                'data' => $this->text(), // auxiliary data
            ], $tableOptions);
        }

        if (!in_array('story_parameter', $tables)) {
            $this->createTable('{{%story_parameter}}', [
                'story_parameter_id' => $this->primaryKey()->unsigned(),
                'story_id' => $this->integer(11)->unsigned(),
                'code' => $this->string(20)->notNull(),
                'visibility' => 'ENUM(\'full\',\'logged\',\'gm\',\'none\') NOT NULL DEFAULT \'logged\'',
                'content' => $this->string(80)->notNull(),
            ], $tableOptions);
        }

        if (!in_array('user', $tables)) {
            $this->createTable('{{%user}}', [
                'id' => $this->primaryKey()->unsigned(),
                'username' => $this->string()->notNull()->unique(),
                'auth_key' => $this->string(32)->notNull(),
                'password_hash' => $this->string()->notNull(),
                'password_reset_token' => $this->string()->unique(),
                'email' => $this->string()->notNull()->unique(),
                'status' => $this->smallInteger()->notNull()->defaultValue(10),
                'created_at' => $this->integer()->notNull(),
                'updated_at' => $this->integer()->notNull(),
                'language' => $this->char(2)->notNull()->defaultValue('en'),
            ], $tableOptions);
        }

        $this->execute('SET foreign_key_checks = 0');
        $this->addForeignKey('character_ibfk_1', '{{%character}}', 'epic_id', '{{%epic}}', 'epic_id', 'RESTRICT', 'CASCADE');
        $this->addForeignKey('description_ibfk_1', '{{%description}}', 'description_pack_id', '{{%description_pack}}', 'description_pack_id', 'RESTRICT', 'CASCADE');
        $this->addForeignKey('group_ibfk_1', '{{%group}}', 'epic_id', '{{%epic}}', 'epic_id', 'RESTRICT', 'CASCADE');
        $this->addForeignKey('person_ibfk_1', '{{%person}}', 'epic_id', '{{%epic}}', 'epic_id', 'RESTRICT', 'CASCADE');
        $this->addForeignKey('person_ibfk_2', '{{%person}}', 'character_id', '{{%character}}', 'character_id', 'RESTRICT', 'CASCADE');
        $this->addForeignKey('recap_ibfk_1', '{{%recap}}', 'epic_id', '{{%epic}}', 'epic_id', 'RESTRICT', 'CASCADE');
        $this->addForeignKey('story_ibfk_1', '{{%story}}', 'epic_id', '{{%epic}}', 'epic_id', 'RESTRICT', 'CASCADE');
        $this->addForeignKey('story_parameter_ibfk_1', '{{%story_parameter}}', 'story_parameter_id', '{{%story}}', 'story_id', 'RESTRICT', 'CASCADE');

        $this->execute('SET foreign_key_checks = 1;');
    }

    public function safeDown()
    {
        $this->execute('SET foreign_key_checks = 0;');
        $this->dropTable('{{%character}}');
        $this->dropTable('{{%description}}');
        $this->dropTable('{{%description_pack}}');
        $this->dropTable('{{%group}}');
        $this->dropTable('{{%person}}');
        $this->dropTable('{{%recap}}');
        $this->dropTable('{{%story}}');
        $this->dropTable('{{%story_parameter}}');
        $this->dropTable('{{%user}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}
