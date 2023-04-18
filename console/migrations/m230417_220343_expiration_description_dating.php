<?php

use yii\db\Migration;

/**
 * Class m230417_220343_expiration_description_dating
 */
class m230417_220343_expiration_description_dating extends Migration
{
    public function safeUp()
    {
        $this->addColumn('description', 'point_in_time_end_id', $this->integer()->unsigned());
        $this->addColumn('description_history', 'point_in_time_end_id', $this->integer()->unsigned());

        $this->addForeignKey(
            'description_expiration_point_in_time',
            'description', 'point_in_time_end_id',
            'point_in_time', 'point_in_time_id',
            'RESTRICT', 'CASCADE'
        );

        $this->addForeignKey(
            'description_history_expiration_point_in_time',
            'description_history', 'point_in_time_end_id',
            'point_in_time', 'point_in_time_id',
            'RESTRICT', 'CASCADE'
        );

        $this->renameColumn('description', 'point_in_time_id', 'point_in_time_start_id');
        $this->renameColumn('description_history', 'point_in_time_id', 'point_in_time_start_id');
    }

    public function safeDown()
    {
        $this->renameColumn('description_history', 'point_in_time_start_id', 'point_in_time_id');
        $this->renameColumn('description', 'point_in_time_start_id', 'point_in_time_id');

        $this->dropForeignKey('description_history_expiration_point_in_time', 'description_history');
        $this->dropForeignKey('description_expiration_point_in_time', 'description');

        $this->dropColumn('description_history', 'point_in_time_end_id');
        $this->dropColumn('description', 'point_in_time_end_id');
    }
}
