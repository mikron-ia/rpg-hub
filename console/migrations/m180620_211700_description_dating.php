<?php

use yii\db\Migration;

class m180620_211700_description_dating extends Migration
{

    public function up(): void
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

    public function down(): void
    {
        $this->dropForeignKey('description_history_point_in_time', 'description_history');
        $this->dropForeignKey('description_point_in_time', 'description');

        $this->dropColumn('description_history', 'point_in_time_id');
        $this->dropColumn('description', 'point_in_time_id');
    }
}
