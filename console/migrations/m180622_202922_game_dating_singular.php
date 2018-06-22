<?php

use yii\db\Migration;

/**
 * Class m180622_202922_game_dating_singular
 */
class m180622_202922_game_dating_singular extends Migration
{
    public function safeUp()
    {
        $this->addColumn('game', 'planned_date', $this->date()->after('basics'));
        $this->addColumn('game', 'planned_time', $this->time()->after('planned_date'));
    }

    public function safeDown()
    {
        $this->dropColumn('game', 'planned_time');
        $this->dropColumn('game', 'planned_date');
    }
}
