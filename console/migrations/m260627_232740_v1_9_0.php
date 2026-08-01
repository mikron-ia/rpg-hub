<?php

use yii\db\Migration;

class m260627_232740_v1_9_0 extends Migration
{
    public function safeUp(): void
    {
        $this->addColumn('{{%project}}', 'bestowed_list_id', $this->integer(11)->unsigned()->after('based_on_id'));

        $this->addForeignKey(
            'project_bestowed_list',
            '{{%project}}',
            'bestowed_list_id',
            '{{%bestowed_list}}',
            'bestowed_list_id',
            'RESTRICT',
            'CASCADE'
        );
    }

    public function safeDown(): void
    {
        $this->dropForeignKey('project_bestowed_list', '{{%project}}');

        $this->dropColumn('{{%project}}', 'bestowed_list_id');
    }
}
