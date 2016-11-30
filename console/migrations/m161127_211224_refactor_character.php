<?php

use yii\db\Migration;

/**
 * Class m161127_211224_refactor_character
 * @todo Adopt into v0.6.0
 */
class m161127_211224_refactor_character extends Migration
{
    public function up()
    {
        $tableOptions = "CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB";

        $this->createTable('{{%character_sheet}}', [
            'character_sheet_id' => $this->primaryKey()->unsigned(),
            'epic_id' => $this->integer(11)->unsigned()->notNull(),
            'key' => $this->string(80)->notNull(),
            'name' => $this->string(120)->notNull(),
            'data' => $this->text()->notNull(),
            'currently_delivered_person_id' => $this->integer(11)->unsigned(),
            'player_id' => $this->integer(10)->unsigned(),
        ], $tableOptions);

        $this->addForeignKey('character_sheet_ibfk_1', '{{%character_sheet}}', 'epic_id', '{{%epic}}', 'epic_id', 'RESTRICT', 'CASCADE');
        $this->addForeignKey('character_sheet_ibfk_2', '{{%character_sheet}}', 'currently_delivered_person_id', 'person', 'person_id', 'RESTRICT', 'CASCADE');
        $this->addForeignKey('character_sheet_ibfk_3', '{{%character_sheet}}', 'player_id', '{{%user}}', 'id', 'RESTRICT', 'CASCADE');

        $this->execute('INSERT INTO `character_sheet` SELECT * FROM `character`');

        $this->dropForeignKey('person_ibfk_2', '{{%person}}');
        $this->addColumn('person', 'character_sheet_id', $this->integer(11)->unsigned()->after('character_id'));
        $this->execute('UPDATE `person` SET character_sheet_id = character_id');
        $this->dropColumn('person', 'character_id');
        $this->addForeignKey('person_ibfk_2', '{{%person}}', 'character_sheet_id', '{{%character_sheet}}', 'character_sheet_id', 'RESTRICT', 'CASCADE');

        $this->dropTable('character');
    }

    public function down()
    {
        $tableOptions = "CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB";

        $this->createTable('{{%character}}', [
            'character_id' => $this->primaryKey()->unsigned(),
            'epic_id' => $this->integer(11)->unsigned()->notNull(),
            'key' => $this->string(80)->notNull(),
            'name' => $this->string(120)->notNull(),
            'data' => $this->text()->notNull(),
            'currently_delivered_person_id' => $this->integer(11)->unsigned(),
            'player_id' => $this->integer(10)->unsigned(),
        ], $tableOptions);

        $this->execute('INSERT INTO `character` SELECT * FROM `character_sheet`');

        $this->execute('SET foreign_key_checks = 0;');

        $this->dropForeignKey('person_ibfk_2', '{{%person}}');

        $this->addColumn('person', 'character_id', $this->integer(11)->unsigned()->after('character_sheet_id'));
        $this->execute('UPDATE `person` SET character_id = character_sheet_id');
        $this->dropColumn('person', 'character_sheet_id');

        $this->addForeignKey('character_ibfk_1', 'character', 'epic_id', '{{%epic}}', 'epic_id', 'RESTRICT', 'CASCADE');
        $this->addForeignKey('character_ibfk_2', 'character', 'currently_delivered_person_id', 'person', 'person_id', 'RESTRICT', 'CASCADE');
        $this->addForeignKey('character_ibfk_3', 'character', 'player_id', '{{%user}}', 'id', 'RESTRICT', 'CASCADE');

        $this->addForeignKey('person_ibfk_2', '{{%person}}', 'character_id', '{{%character}}', 'character_id', 'RESTRICT', 'CASCADE');

        $this->dropTable('{{%character_sheet}}');

        $this->execute('SET foreign_key_checks = 1;');
    }
}
