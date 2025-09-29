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

        $this->addColumn('story', 'based_on_id', $this->integer(10)->unsigned()->after('visibility')->null());

        $this->addForeignKey(
            'story_based_on_ibfk1',
            'story',
            'based_on_id',
            'scenario',
            'scenario_id',
            'RESTRICT',
            'CASCADE'
        );
    }

    public function safeDown(): void
    {
        $this->dropForeignKey('story_based_on_ibfk1', 'story');

        $this->dropColumn('story', 'based_on_id');

        $this->dropColumn('scenario', 'content_expanded');
        $this->dropColumn('scenario', 'content');

        $this->dropColumn('parameter_pack', 'parameters_gm');
        $this->dropColumn('parameter_pack', 'parameters_full');
    }
}
