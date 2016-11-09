<?php

use yii\db\Migration;

class m161109_184132_v0_5_0 extends Migration
{
    public function up()
    {
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

        $this->execute('SET foreign_key_checks = 1;');
    }
}
