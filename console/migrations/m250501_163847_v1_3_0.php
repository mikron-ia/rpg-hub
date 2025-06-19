<?php

use yii\db\Migration;

class m250501_163847_v1_3_0 extends Migration
{
    public function safeUp(): void
    {
        $this->renameColumn('{{%recap}}', 'data', 'content');
        $this->addColumn('{{%recap}}', 'content_expanded', $this->text()->after('content')->defaultValue(null));

        $this->addColumn('{{%description}}', 'point_in_time_still_valid_id', $this->integer()->unsigned());
        $this->addColumn('{{%description_history}}', 'point_in_time_still_valid_id', $this->integer()->unsigned());

        $this->addForeignKey(
            'description_point_in_time_still_valid',
            'description', 'point_in_time_still_valid_id',
            'point_in_time', 'point_in_time_id',
            'RESTRICT', 'CASCADE'
        );

        $this->addForeignKey(
            'description_history_point_in_time_still_valid',
            'description_history', 'point_in_time_still_valid_id',
            'point_in_time', 'point_in_time_id',
            'RESTRICT', 'CASCADE'
        );
    }

    public function safeDown(): void
    {
        $this->dropForeignKey('description_history_point_in_time_still_valid', 'description_history');
        $this->dropForeignKey('description_point_in_time_still_valid', 'description');

        $this->dropColumn('{{%description_history}}', 'point_in_time_still_valid_id');
        $this->dropColumn('{{%description}}', 'point_in_time_still_valid_id');

        $this->dropColumn('{{%recap}}', 'content_expanded');
        $this->renameColumn('{{%recap}}', 'content', 'data');
    }
}
