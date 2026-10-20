<?php

use yii\db\Migration;

class m180622_202922_game_dating_singular extends Migration
{
    public function up(): void
    {
        $this->addColumn('game', 'planned_date', $this->date()->after('basics'));
    }

    public function down(): void
    {
        $this->dropColumn('game', 'planned_date');
    }
}
