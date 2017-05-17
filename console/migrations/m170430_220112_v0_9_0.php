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

        $tableOptions = "CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB";

        $this->createTable('article', [
            'article_id' => $this->primaryKey()->unsigned(),
            'epic_id' => $this->integer(11)->unsigned(),
            'key' => $this->string(80)->notNull(),
            'title' => $this->string(120)->notNull(),
            'subtitle' => $this->string(120),
            'visibility' => $this->string(20)->notNull()->defaultValue(Visibility::VISIBILITY_GM),
            'text_raw' => $this->text()->notNull(),
            'text_ready' => $this->text()->notNull(),
            'seen_pack_id' => $this->integer(11)->unsigned(),
        ], $tableOptions);

        $this->addForeignKey('article_epic', 'article', 'epic_id', '{{%epic}}', 'epic_id', 'RESTRICT', 'CASCADE');
        $this->addForeignKey('article_seen', 'article', 'seen_pack_id', 'seen_pack', 'seen_pack_id', 'RESTRICT', 'CASCADE');
    }

    public function down()
    {
        $this->dropTable('article');

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
