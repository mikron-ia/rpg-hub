<?php

use yii\db\Migration;

class m170113_161413_v0_7_0 extends Migration
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

        $this->truncateTable('{{%auth_assignment}}');
        $this->truncateTable('{{%auth_item}}');
        $this->truncateTable('{{%auth_item_child}}');
        $this->truncateTable('{{%auth_rule}}');
        $this->truncateTable('{{%character}}');
        $this->truncateTable('{{%character_sheet}}');
        $this->truncateTable('{{%description}}');
        $this->truncateTable('{{%description_pack}}');
        $this->truncateTable('{{%epic}}');
        $this->truncateTable('{{%external_data}}');
        $this->truncateTable('{{%external_data_pack}}');
        $this->truncateTable('{{%group}}');
        $this->truncateTable('{{%ip}}');
        $this->truncateTable('{{%parameter}}');
        $this->truncateTable('{{%parameter_pack}}');
        $this->truncateTable('{{%participant}}');
        $this->truncateTable('{{%participant_role}}');
        $this->truncateTable('{{%performed_action}}');
        $this->truncateTable('{{%recap}}');
        $this->truncateTable('{{%story}}');
        $this->truncateTable('{{%story_parameter}}');
        $this->truncateTable('{{%user}}');
        $this->truncateTable('{{%user_agent}}');
        $this->truncateTable('{{%user_invitation}}');

        $this->execute('SET foreign_key_checks = 1;');
    }
}
