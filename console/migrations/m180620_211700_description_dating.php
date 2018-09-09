<?php

use yii\db\Migration;

/**
 * Class m180620_211700_description_dating
 */
class m180620_211700_description_dating extends Migration
{

    public function safeUp()
    {
        $this->addColumn('description', 'point_in_time_id', $this->integer()->unsigned());
        $this->addColumn('description_history', 'point_in_time_id', $this->integer()->unsigned());

        $this->addForeignKey(
            'description_point_in_time',
            'description', 'point_in_time_id',
            'point_in_time', 'point_in_time_id',
            'RESTRICT', 'CASCADE'
        );

        $this->addForeignKey(
            'description_history_point_in_time',
            'description_history', 'point_in_time_id',
            'point_in_time', 'point_in_time_id',
            'RESTRICT', 'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('description_history_point_in_time', 'description_history');
        $this->dropForeignKey('description_point_in_time', 'description');

        $this->dropColumn('description_history', 'point_in_time_id');
        $this->dropColumn('description', 'point_in_time_id');
    }
}
