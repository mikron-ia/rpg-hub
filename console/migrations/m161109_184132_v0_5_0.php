<?php

use yii\db\Migration;

class m161109_184132_v0_5_0 extends Migration
{
    public function up()
    {
        $tableOptions = "CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB";

        /* external_datas */
        $this->createTable('{{%external_data_pack}}', [
            'external_data_pack_id' => $this->primaryKey()->unsigned(),
            'class' => $this->string(20)->notNull()->comment("Name of class this pack belongs to; necessary for proper type assignment"),
            'name' => $this->string(80)->notNull(),
        ], $tableOptions);

        $this->createTable('{{%external_data}}', [
            'external_data_id' => $this->primaryKey()->unsigned(),
            'external_data_pack_id' => $this->integer(11)->unsigned(),
            'code' => $this->string(40)->notNull(),
            'data' => $this->text()->notNull(),
            'visibility' => $this->string(20)->notNull()->defaultValue(\common\models\core\Visibility::VISIBILITY_GM),
        ], $tableOptions);

        $this->addColumn('person', 'external_data_pack_id', $this->integer(11)->unsigned());

        /**
         * Loading up data, if available
         * This is a stopgap measure that should be moved forward to newest migration and removed no later than in 1.0
         */
        $scriptName = __DIR__ . '/' . 'data.sql';
        if (file_exists($scriptName)) {
            $scriptContent = file_get_contents($scriptName);
            $this->execute($scriptContent);
        }
    }

    public function down()
    {

        $this->execute('SET foreign_key_checks = 0;');

        $this->truncateTable('{{%character}}');
        $this->truncateTable('{{%description}}');
        $this->truncateTable('{{%description_pack}}');
        $this->truncateTable('{{%epic}}');
        $this->truncateTable('{{%group}}');
        $this->truncateTable('{{%parameter}}');
        $this->truncateTable('{{%parameter_pack}}');
        $this->truncateTable('{{%participant}}');
        $this->truncateTable('{{%participant_role}}');
        $this->truncateTable('{{%performed_action}}');
        $this->truncateTable('{{%person}}');
        $this->truncateTable('{{%recap}}');
        $this->truncateTable('{{%story}}');
        $this->truncateTable('{{%story_parameter}}');
        $this->truncateTable('{{%user}}');

        $this->dropColumn('person', 'external_data_pack_id');

        $this->dropTable('{{%external_data}}');
        $this->dropTable('{{%external_data_pack}}');

        $this->execute('SET foreign_key_checks = 1;');
    }
}
