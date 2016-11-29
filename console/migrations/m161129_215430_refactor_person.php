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
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0;');
        $this->dropTable('character');
        $this->execute('SET foreign_key_checks = 1;');
    }
}
