<?php

use yii\db\Migration;

class m160527_115621_v0_2_0 extends Migration
{
    public function safeUp()
    {
        $this->addColumn('character', 'currently_delivered_person_id', $this->integer(11)->unsigned());
        $this->addForeignKey(
            'character_ibfk_3', 'character', 'currently_delivered_person_id',
            'person', 'person_id',
            'RESTRICT', 'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('character_ibfk_3', 'character');
        $this->dropColumn('character', 'currently_delivered_person_id');
    }
}
