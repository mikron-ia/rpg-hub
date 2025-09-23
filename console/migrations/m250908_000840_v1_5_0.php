<?php

use yii\db\Migration;

class m250908_000840_v1_5_0 extends Migration
{
    public function safeUp(): void
    {
        $this->addColumn('parameter_pack', 'parameters_full', $this->json()->defaultValue(json_encode([]))->notNull());
        $this->addColumn('parameter_pack', 'parameters_gm', $this->json()->defaultValue(json_encode([]))->notNull());

        $this->addColumn('scenario', 'content', $this->text()->after('tag_line')->null());
        $this->addColumn('scenario', 'content_expanded', $this->text()->after('content')->null());
    }

    public function safeDown(): void
    {
        $this->dropColumn('scenario', 'content_expanded');
        $this->dropColumn('scenario', 'content');

        $this->dropColumn('parameter_pack', 'parameters_gm');
        $this->dropColumn('parameter_pack', 'parameters_full');
    }
}
