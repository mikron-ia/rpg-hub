<?php

use yii\db\Migration;

class m161203_152029_external_data_timestamping extends Migration
{
    public function up()
    {
        $this->addColumn('external_data', 'created_at', $this->integer()->notNull());
        $this->addColumn('external_data', 'updated_at', $this->integer()->notNull());
        $this->addColumn('external_data', 'created_by', $this->integer(10)->notNull()->unsigned());
        $this->addColumn('external_data', 'updated_by', $this->integer(10)->notNull()->unsigned());

        $this->addColumn('external_data_pack', 'created_at', $this->integer()->notNull());
        $this->addColumn('external_data_pack', 'updated_at', $this->integer()->notNull());

        $this->execute('UPDATE external_data SET `created_at` = ' . time());
        $this->execute('UPDATE external_data SET `updated_at` = ' . time());
        $this->execute('UPDATE external_data_pack SET `created_at` = ' . time());
        $this->execute('UPDATE external_data_pack SET `updated_at` = ' . time());

        $this->execute('UPDATE external_data SET `created_by` = 1');
        $this->execute('UPDATE external_data SET `updated_by` = 1');

        $this->addForeignKey('external_data_creator', 'external_data', 'created_by', 'user', 'id');
        $this->addForeignKey('external_data_updater', 'external_data', 'updated_by', 'user', 'id');
    }

    public function down()
    {
        $this->dropForeignKey('external_data_creator', 'external_data');
        $this->dropForeignKey('external_data_updater', 'external_data');

        $this->dropColumn('external_data', 'created_at');
        $this->dropColumn('external_data', 'updated_at');
        $this->dropColumn('external_data', 'created_by');
        $this->dropColumn('external_data', 'updated_by');

        $this->dropColumn('external_data_pack', 'created_at');
        $this->dropColumn('external_data_pack', 'updated_at');
    }
}
