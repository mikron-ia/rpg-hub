<?php

use common\models\core\Visibility;
use yii\db\Migration;

class m170430_220112_v0_9_0 extends Migration
{
    public function up()
    {
        /**
         * Loading up data, if available
         * This is a stopgap measure that should be moved forward to newest migration and removed no later than in 1.0
         * File must contain data that conform to structure established for 0.8.0 and must not contain any data for `migration` table
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
        $this->truncateTable('{{%seen}}');
        $this->truncateTable('{{%seen_pack}}');
        $this->truncateTable('{{%story}}');
        $this->truncateTable('{{%story_parameter}}');
        $this->truncateTable('{{%user}}');
        $this->truncateTable('{{%user_agent}}');
        $this->truncateTable('{{%user_invitation}}');

        $this->execute('SET foreign_key_checks = 1;');
    }
}
