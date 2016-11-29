<?php

use yii\db\Migration;

class m161129_215430_refactor_person extends Migration
{
    public function up()
    {
        $tableOptions = "CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB";

        $this->createTable('character', [
            'character_id' => $this->primaryKey()->unsigned(),
            'epic_id' => $this->integer(11)->unsigned()->notNull(),
            'key' => $this->string(80)->notNull(),
            'name' => $this->string(120)->notNull(),
            'tagline' => $this->string(120)->notNull(),
            'data' => $this->text()->notNull(),
            'visibility' => $this->string(20)->notNull(),
            'character_sheet_id' => $this->integer(11)->unsigned(),
            'description_pack_id' => $this->integer(11)->unsigned(),
            'external_data_pack_id' => $this->integer(11)->unsigned(),
        ], $tableOptions);

        $this->execute('INSERT INTO `character` SELECT * FROM `person`');

        $this->addForeignKey('character_ibfk_1', 'character', 'epic_id', '{{%epic}}', 'epic_id', 'RESTRICT', 'CASCADE');
        $this->addForeignKey('character_ibfk_2', 'character', 'character_sheet_id', 'character_sheet', 'character_sheet_id', 'RESTRICT', 'CASCADE');
        $this->addForeignKey('character_ibfk_3', 'character', 'description_pack_id', 'description_pack', 'description_pack_id', 'RESTRICT', 'CASCADE');
        $this->addForeignKey('character_ibfk_4', 'character', 'external_data_pack_id', 'external_data_pack', 'external_data_pack_id', 'RESTRICT', 'CASCADE');

        $this->dropForeignKey('character_sheet_ibfk_2', '{{%character_sheet}}');
        $this->addForeignKey('character_sheet_ibfk_2', '{{%character_sheet}}', 'currently_delivered_person_id', 'character', 'character_id', 'RESTRICT', 'CASCADE');

        $this->dropTable('person');
    }

    public function down()
    {
        $tableOptions = "CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB";

        $this->createTable('person', [
            'person_id' => $this->primaryKey()->unsigned(),
            'epic_id' => $this->integer(11)->unsigned()->notNull(),
            'key' => $this->string(80)->notNull(),
            'name' => $this->string(120)->notNull(),
            'tagline' => $this->string(120)->notNull(),
            'data' => $this->text()->notNull(),
            'visibility' => $this->string(20)->notNull(),
            'character_sheet_id' => $this->integer(11)->unsigned(),
            'description_pack_id' => $this->integer(11)->unsigned(),
            'external_data_pack_id' => $this->integer(11)->unsigned(),
        ], $tableOptions);

        $this->execute('INSERT INTO `person` SELECT * FROM `character`');

        $this->dropForeignKey('character_sheet_ibfk_2', '{{%character_sheet}}');
        $this->addForeignKey('character_sheet_ibfk_2', '{{%character_sheet}}', 'currently_delivered_person_id', 'person', 'person_id', 'RESTRICT', 'CASCADE');

        $this->dropForeignKey('character_ibfk_1', 'character');
        $this->dropForeignKey('character_ibfk_2', 'character');
        $this->dropForeignKey('character_ibfk_3', 'character');
        $this->dropForeignKey('character_ibfk_4', 'character');

        $this->addForeignKey('person_ibfk_1', 'person', 'epic_id', '{{%epic}}', 'epic_id', 'RESTRICT', 'CASCADE');
        $this->addForeignKey('person_ibfk_2', 'person', 'character_sheet_id', 'character_sheet', 'character_sheet_id', 'RESTRICT', 'CASCADE');
        $this->addForeignKey('person_ibfk_3', 'person', 'description_pack_id', 'description_pack', 'description_pack_id', 'RESTRICT', 'CASCADE');
        $this->addForeignKey('person_ibfk_4', 'person', 'external_data_pack_id', 'external_data_pack', 'external_data_pack_id', 'RESTRICT', 'CASCADE');

        $this->dropTable('character');
    }
}
