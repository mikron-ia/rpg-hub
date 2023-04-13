<?php

use yii\db\Migration;

/**
 * Class m230412_235601_link_session_to_recap
 */
class m230412_235601_link_session_to_recap extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%game}}', 'recap_id', $this->integer(11)->unsigned()->after('notes'));

        $this->addForeignKey('game_recap', '{{%game}}', 'recap_id', '{{%recap}}', 'recap_id', 'RESTRICT', 'CASCADE');
    }

    public function safeDown()
    {
        $this->dropForeignKey('game_recap', '{{%game}}');
        $this->dropColumn('{{%game}}', 'recap_id');
    }
}
