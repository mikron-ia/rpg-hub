<?php

use common\models\core\Visibility;
use yii\db\Migration;

/**
 * Class m160602_230625_v0_3_0
 *
 * Consolidates previous migrations
 */
class m160602_230625_v0_3_0 extends Migration
{
    public function safeUp()
    {
        $tableOptions = "CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB";

        /* Descriptions */
        $this->createTable('{{%description_pack}}', [
            'description_pack_id' => $this->primaryKey()->unsigned(),
            'class' => $this->string(20)->notNull()->comment("Name of class this pack belongs to; necessary for proper type assignment"),
            'name' => $this->string(80)->notNull(),
        ], $tableOptions);

        $this->createTable('{{%description}}', [
            'description_id' => $this->primaryKey()->unsigned(),
            'description_pack_id' => $this->integer(11)->unsigned(),
            'code' => $this->string(40)->notNull(),
            'public_text' => $this->text()->notNull(),
            'private_text' => $this->text(),
            'lang' => $this->string(5)->notNull()->defaultValue('en'),
            'visibility' => $this->string(20)->notNull()->defaultValue(Visibility::VISIBILITY_NONE),
            'position' => $this->integer()->defaultValue(0),
        ], $tableOptions);

        /* Parameters */
        $this->createTable('{{%parameter_pack}}', [
            'parameter_pack_id' => $this->primaryKey()->unsigned(),
            'class' => $this->string(20)->notNull()->comment("Name of class this pack belongs to; necessary for proper type assignment"),
            'name' => $this->string(80)->notNull(),
        ], $tableOptions);

        $this->createTable('{{%parameter}}', [
            'parameter_id' => $this->primaryKey()->unsigned(),
            'parameter_pack_id' => $this->integer(11)->unsigned(),
            'code' => $this->string(20)->notNull(),
            'lang' => $this->string(5),
            'visibility' => $this->string(20)->notNull()->defaultValue(Visibility::VISIBILITY_NONE),
            'position' => $this->integer()->defaultValue(0),
            'content' => $this->string(120)->notNull(),
            'FOREIGN KEY (parameter_pack_id) REFERENCES `parameter_pack` (parameter_pack_id) ON DELETE RESTRICT ON UPDATE CASCADE',
        ], $tableOptions);

        /* Objects */

        $this->createTable('{{%epic}}', [
            'epic_id' => $this->primaryKey()->unsigned(),
            'key' => $this->string(80)->notNull(),
            'name' => $this->string(80)->notNull()->comment('Public name for the epic'),
            'system' => $this->string(20)->notNull()->comment('Code for the system used'),
            'parameter_pack_id' => $this->integer(11)->unsigned(),
            'FOREIGN KEY (parameter_pack_id) REFERENCES `parameter_pack` (parameter_pack_id) ON DELETE RESTRICT ON UPDATE CASCADE',
        ], $tableOptions);

        $this->createTable('{{%group}}', [
            'group_id' => $this->primaryKey()->unsigned(),
            'epic_id' => $this->integer(11)->unsigned()->notNull(),
            'key' => $this->string(80)->notNull(),
            'name' => $this->string(120)->notNull(),
            'data' => $this->text()->notNull(),
        ], $tableOptions);

        $this->createTable('{{%recap}}', [
            'recap_id' => $this->primaryKey()->unsigned(),
            'epic_id' => $this->integer(11)->unsigned()->notNull(),
            'key' => $this->string(80)->notNull(),
            'name' => $this->string(120)->notNull(),
            'data' => $this->text()->notNull(),
            'time' => $this->dateTime()->notNull(),
        ], $tableOptions);

        $this->createTable('{{%story}}', [
            'story_id' => $this->primaryKey()->unsigned(),
            'epic_id' => $this->integer(11)->unsigned()->notNull(),
            'key' => $this->string(80)->notNull(),
            'name' => $this->string(120)->notNull(),
            'short' => $this->text()->notNull(),
            'long' => $this->text(),
            'position' => $this->integer()->defaultValue(0),
            'data' => $this->text(),
            'parameter_pack_id' => $this->integer(11)->unsigned(),
            'FOREIGN KEY (parameter_pack_id) REFERENCES `parameter_pack` (parameter_pack_id) ON DELETE RESTRICT ON UPDATE CASCADE',
            'FOREIGN KEY (epic_id) REFERENCES `epic` (epic_id) ON DELETE RESTRICT ON UPDATE CASCADE',
        ], $tableOptions);

        $this->createTable('{{%story_parameter}}', [
            'story_parameter_id' => $this->primaryKey()->unsigned(),
            'story_id' => $this->integer(11)->unsigned(),
            'code' => $this->string(20)->notNull(),
            'visibility' => $this->string(20)->notNull()->defaultValue(Visibility::VISIBILITY_NONE),
            'content' => $this->string(80)->notNull(),
        ], $tableOptions);

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
            'language' => $this->char(5)->notNull()->defaultValue('en'),
        ], $tableOptions);

        $this->execute('SET foreign_key_checks = 0');

        $this->addForeignKey('description_ibfk_1', '{{%description}}', 'description_pack_id', '{{%description_pack}}', 'description_pack_id', 'RESTRICT', 'CASCADE');
        $this->addForeignKey('group_ibfk_1', '{{%group}}', 'epic_id', '{{%epic}}', 'epic_id', 'RESTRICT', 'CASCADE');
        $this->addForeignKey('recap_ibfk_1', '{{%recap}}', 'epic_id', '{{%epic}}', 'epic_id', 'RESTRICT', 'CASCADE');
        $this->addForeignKey('story_parameter_ibfk_1', '{{%story_parameter}}', 'story_id', '{{%story}}', 'story_id', 'RESTRICT', 'CASCADE');

        $this->execute('SET foreign_key_checks = 1;');

        $this->execute("ALTER TABLE `group` CHANGE `data` `data` LONGTEXT NOT NULL;"); // patch for large values stored as story data
    }

    public function safeDown()
    {
        $this->execute('SET foreign_key_checks = 0;');
        $this->dropTable('{{%description}}');
        $this->dropTable('{{%description_pack}}');
        $this->dropTable('{{%epic}}');
        $this->dropTable('{{%group}}');
        $this->dropTable('{{%parameter}}');
        $this->dropTable('{{%parameter_pack}}');
        $this->dropTable('{{%recap}}');
        $this->dropTable('{{%story}}');
        $this->dropTable('{{%story_parameter}}');
        $this->dropTable('{{%user}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}
