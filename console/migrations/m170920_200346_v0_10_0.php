<?php

use yii\db\Migration;

class m170920_200346_v0_10_0 extends Migration
{
    public function safeUp()
    {
		/**
		 * Loading up data, if available
		 * This is a stopgap measure that should be moved forward to newest migration and removed no later than in 1.0
		 * File must contain data that conform to structure established for 0.9.0 and must not contain any data for `migration` table
		 * Data in the file should not be contained in transaction - it may prevent migration from properly recording
		 */
		$scriptName = __DIR__ . '/' . 'data.sql';
		if (file_exists($scriptName)) {
			$this->execute('SET foreign_key_checks = 0;');
			$scriptContent = file_get_contents($scriptName);
			$this->execute($scriptContent);
			$this->execute('SET foreign_key_checks = 1;');
		}

        /* Story typing */
        $this->addColumn('story', 'code', $this->string(40)->notNull()->after('visibility'));

        /* Protected descriptions */
        $this->addColumn('description', 'protected_text', $this->text()->after('public_text'));

        /* Flag pack */
        /* @todo Create the table */
        /* @todo Attach to tables of flaggable objects - character and group */

        /* System of TO DO records for players */
        /* @todo Create the table */
    }

    public function safeDown()
    {
        $this->dropColumn('story', 'code');
        $this->dropColumn('description', 'protected_text');

		$this->execute('SET foreign_key_checks = 0;');

		$this->truncateTable('{{%auth_assignment}}');
		$this->truncateTable('{{%auth_item}}');
		$this->truncateTable('{{%auth_item_child}}');
		$this->truncateTable('{{%auth_rule}}');
		$this->truncateTable('{{%character}}');
		$this->truncateTable('{{%character_sheet}}');
		$this->truncateTable('{{%description}}');
		$this->truncateTable('{{%description_history}}');
		$this->truncateTable('{{%description_pack}}');
		$this->truncateTable('{{%epic}}');
		$this->truncateTable('{{%external_data}}');
		$this->truncateTable('{{%external_data_pack}}');
		$this->truncateTable('{{%game}}');
		$this->truncateTable('{{%group}}');
		$this->truncateTable('{{%group_membership}}');
		$this->truncateTable('{{%group_membership_history}}');
		$this->truncateTable('{{%ip}}');
		$this->truncateTable('{{%parameter}}');
		$this->truncateTable('{{%parameter_pack}}');
		$this->truncateTable('{{%participant}}');
		$this->truncateTable('{{%participant_role}}');
		$this->truncateTable('{{%performed_action}}');
		$this->truncateTable('{{%recap}}');
		$this->truncateTable('{{%scenario}}');
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
