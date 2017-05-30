<?php

use common\models\core\ImportanceCategory;
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
            'seen_pack_id' => $this->integer(11)->unsigned(),
            'description_pack_id' => $this->integer(11)->unsigned(),
            'position' => $this->integer()->defaultValue(0),
            'text_raw' => $this->text()->notNull(),
            'text_ready' => $this->text()->notNull(),
        ], $tableOptions);

        $this->addForeignKey('article_epic', 'article', 'epic_id', '{{%epic}}', 'epic_id', 'RESTRICT', 'CASCADE');
        $this->addForeignKey('article_seen', 'article', 'seen_pack_id', 'seen_pack', 'seen_pack_id', 'RESTRICT', 'CASCADE');
        $this->addForeignKey('article_description', 'article', 'description_pack_id', 'description_pack', 'description_pack_id', 'RESTRICT', 'CASCADE');

        $this->addColumn('character', 'importance_category', $this->string(20)->notNull()->defaultValue(ImportanceCategory::IMPORTANCE_MEDIUM)->after('importance'));
        $this->execute("UPDATE `character` SET importance_category = importance");
        $this->dropColumn('character', 'importance');

        $this->addColumn('group', 'external_data_pack_id', $this->integer(11)->unsigned());
        $this->addForeignKey('group_external_data', 'group', 'external_data_pack_id', 'external_data_pack', 'external_data_pack_id', 'RESTRICT', 'CASCADE');

        /* Descriptions */
        $this->createTable('{{%importance_pack}}', [
            'importance_pack_id' => $this->primaryKey()->unsigned(),
            'class' => $this->string(20)->notNull()->comment("Name of class this pack belongs to; necessary for proper type assignment"),
        ], $tableOptions);

        $this->createTable('{{%importance}}', [
            'importance_id' => $this->primaryKey()->unsigned(),
            'importance_pack_id' => $this->integer(11)->unsigned(),
            'user_id' => $this->integer(10)->unsigned(),
            'importance' => $this->integer(11),
        ], $tableOptions);

        $this->addForeignKey('importance_pack', '{{%importance}}', 'importance_pack_id', '{{%importance_pack}}', 'importance_pack_id', 'RESTRICT', 'CASCADE');
        $this->addForeignKey('importance_user', '{{%importance}}', 'user_id', '{{%user}}', 'id', 'RESTRICT', 'CASCADE');

        $this->addColumn('character', 'importance_pack_id', $this->integer(11)->unsigned());
        $this->addColumn('group', 'importance_pack_id', $this->integer(11)->unsigned());

        $this->addForeignKey('character_importance_pack', '{{%character}}', 'importance_pack_id', '{{%importance_pack}}', 'importance_pack_id', 'RESTRICT', 'CASCADE');
        $this->addForeignKey('group_importance_pack', '{{%group}}', 'importance_pack_id', '{{%importance_pack}}', 'importance_pack_id', 'RESTRICT', 'CASCADE');
    }

    public function down()
    {
        $this->dropForeignKey('group_importance_pack', '{{%group}}');
        $this->dropForeignKey('character_importance_pack', '{{%character}}');

        $this->dropColumn('group', 'importance_pack_id');
        $this->dropColumn('character', 'importance_pack_id');

        $this->dropTable('{{%importance}}');
        $this->dropTable('{{%importance_pack}}');

        $this->dropForeignKey('group_external_data', 'group');
        $this->dropColumn('group', 'external_data_pack_id');

        $this->addColumn('character', 'importance', $this->string(20)->notNull()->defaultValue(ImportanceCategory::IMPORTANCE_MEDIUM)->after('visibility'));
        $this->execute("UPDATE `character` SET importance = importance_category");
        $this->dropColumn('character', 'importance_category');

        $this->dropTable('article');

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
