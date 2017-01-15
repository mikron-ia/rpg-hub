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
            'currently_delivered_character_id' => $this->integer(11)->unsigned(),
            'player_id' => $this->integer(10)->unsigned(),
        ], $tableOptions);

        $this->addForeignKey('character_sheet_epic', '{{%character_sheet}}', 'epic_id', '{{%epic}}', 'epic_id', 'RESTRICT', 'CASCADE');
        $this->addForeignKey('character_sheet_player', '{{%character_sheet}}', 'player_id', '{{%user}}', 'id', 'RESTRICT', 'CASCADE');
    }

    public function down()
    {
        $this->dropTable('{{%character_sheet}}');
    }
}
