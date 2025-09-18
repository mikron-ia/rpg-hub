<?php

use yii\db\Migration;

class m250908_000840_v1_5_0 extends Migration
{
    public function safeUp(): void
    {
        $this->addColumn('parameter_pack', 'parameters_full', $this->json()->defaultValue(json_encode([]))->notNull());
        $this->addColumn('parameter_pack', 'parameters_gm', $this->json()->defaultValue(json_encode([]))->notNull());
    }

    public function safeDown(): void
    {
        $this->dropColumn('parameter_pack', 'parameters_gm');
        $this->dropColumn('parameter_pack', 'parameters_full');
    }
}
