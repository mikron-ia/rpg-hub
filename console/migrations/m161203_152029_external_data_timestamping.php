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

        $this->addColumn('description', 'created_at', $this->integer()->notNull());
        $this->addColumn('description', 'updated_at', $this->integer()->notNull());
        $this->addColumn('description', 'created_by', $this->integer(10)->notNull()->unsigned());
        $this->addColumn('description', 'updated_by', $this->integer(10)->notNull()->unsigned());

        $this->addColumn('description_pack', 'created_at', $this->integer()->notNull());
        $this->addColumn('description_pack', 'updated_at', $this->integer()->notNull());

        $this->execute('UPDATE description SET `created_at` = ' . time());
        $this->execute('UPDATE description SET `updated_at` = ' . time());
        $this->execute('UPDATE description_pack SET `created_at` = ' . time());
        $this->execute('UPDATE description_pack SET `updated_at` = ' . time());

        $this->execute('UPDATE description SET `created_by` = 1');
        $this->execute('UPDATE description SET `updated_by` = 1');

        $this->addForeignKey('description_creator', 'description', 'created_by', 'user', 'id');
        $this->addForeignKey('description_updater', 'description', 'updated_by', 'user', 'id');

        $this->addColumn('parameter', 'created_at', $this->integer()->notNull());
        $this->addColumn('parameter', 'updated_at', $this->integer()->notNull());
        $this->addColumn('parameter', 'created_by', $this->integer(10)->notNull()->unsigned());
        $this->addColumn('parameter', 'updated_by', $this->integer(10)->notNull()->unsigned());

        $this->addColumn('parameter_pack', 'created_at', $this->integer()->notNull());
        $this->addColumn('parameter_pack', 'updated_at', $this->integer()->notNull());

        $this->execute('UPDATE parameter SET `created_at` = ' . time());
        $this->execute('UPDATE parameter SET `updated_at` = ' . time());
        $this->execute('UPDATE parameter_pack SET `created_at` = ' . time());
        $this->execute('UPDATE parameter_pack SET `updated_at` = ' . time());

        $this->execute('UPDATE parameter SET `created_by` = 1');
        $this->execute('UPDATE parameter SET `updated_by` = 1');

        $this->addForeignKey('parameter_creator', 'parameter', 'created_by', 'user', 'id');
        $this->addForeignKey('parameter_updater', 'parameter', 'updated_by', 'user', 'id');
    }

    public function down()
    {
        $this->dropForeignKey('parameter_creator', 'parameter');
        $this->dropForeignKey('parameter_updater', 'parameter');

        $this->dropColumn('parameter', 'created_at');
        $this->dropColumn('parameter', 'updated_at');
        $this->dropColumn('parameter', 'created_by');
        $this->dropColumn('parameter', 'updated_by');

        $this->dropColumn('parameter_pack', 'created_at');
        $this->dropColumn('parameter_pack', 'updated_at');

        $this->dropForeignKey('description_creator', 'description');
        $this->dropForeignKey('description_updater', 'description');

        $this->dropColumn('description', 'created_at');
        $this->dropColumn('description', 'updated_at');
        $this->dropColumn('description', 'created_by');
        $this->dropColumn('description', 'updated_by');

        $this->dropColumn('description_pack', 'created_at');
        $this->dropColumn('description_pack', 'updated_at');

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
