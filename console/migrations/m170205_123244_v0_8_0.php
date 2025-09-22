<?php

use common\models\core\Visibility;
use common\models\Scenario;
use yii\db\Migration;

class m170205_123244_v0_8_0 extends Migration
{
    public function up()
    {
        $this->addColumn('character', 'updated_at', $this->integer()->notNull()->after('importance'));
        $this->update('character', ['updated_at' => time()]);

        $tableOptions = "CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB";

        /* Description history mechanism */
        $this->createTable('{{%description_history}}', [
            'description_history_id' => $this->primaryKey()->unsigned(),
            'description_id' => $this->integer(11)->unsigned(),
            'created_at' => $this->integer(11)->unsigned(),
            'time_ic' => $this->string(),
            'public_text' => $this->text()->notNull(),
            'private_text' => $this->text(),
            'visibility' => $this->string(20)->notNull()->defaultValue(Visibility::VISIBILITY_GM),
        ], $tableOptions);

        $this->addForeignKey(
            'description_history_description',
            'description_history',
            'description_id',
            'description',
            'description_id'
        );

        /* Scenarios */
        $this->createTable('{{%scenario}}', [
            'scenario_id' => $this->primaryKey()->unsigned(),
            'epic_id' => $this->integer(11)->unsigned()->notNull(),
            'key' => $this->string(80)->notNull(),
            'name' => $this->string(120)->notNull(),
            'status' => $this->string(20)->notNull()->defaultValue(Scenario::STATUS_NEW),
            'tag_line' => $this->string(),
            'description_pack_id' => $this->integer(11)->unsigned(),
        ], $tableOptions);

        $this->addForeignKey(
            'scenario_epic',
            'scenario', 'epic_id',
            '{{%epic}}', 'epic_id',
            'RESTRICT', 'CASCADE'
        );
        $this->addForeignKey(
            'scenario_description_pack',
            'scenario', 'description_pack_id',
            '{{%description_pack}}', 'description_pack_id',
            'RESTRICT', 'CASCADE'
        );

        /* Visibility & descriptions for groups */
        $this->addColumn('group', 'visibility', $this->string(20)->notNull()->defaultValue(Visibility::VISIBILITY_GM));
        $this->addColumn('group', 'description_pack_id', $this->integer(11)->unsigned());
        $this->addForeignKey(
            'group_description_pack',
            'group', 'description_pack_id',
            '{{%description_pack}}', 'description_pack_id',
            'RESTRICT', 'CASCADE'
        );

        /* Sessions */
        $this->createTable('{{%game}}', [
            'game_id' => $this->primaryKey()->unsigned(),
            'epic_id' => $this->integer(11)->unsigned()->notNull(),
            'basics' => $this->string()->notNull(),
            'status' => $this->string(20)->notNull()->defaultValue(\common\models\Game::STATUS_PROPOSED),
            'position' => $this->integer()->defaultValue(0),
            'notes' => $this->text(),
        ], $tableOptions);

        $this->addForeignKey(
            'game_epic',
            'game', 'epic_id',
            '{{%epic}}', 'epic_id',
            'RESTRICT', 'CASCADE'
        );

        /* Membership */
        $this->createTable('{{%group_membership}}', [
            'group_membership_id' => $this->primaryKey()->unsigned(),
            'character_id' => $this->integer(11)->unsigned()->notNull(),
            'group_id' => $this->integer(11)->unsigned()->notNull(),
            'visibility' => $this->string(20)->notNull()->defaultValue(Visibility::VISIBILITY_GM),
            'status' => $this->string(20)->notNull()->defaultValue(\common\models\GroupMembership::STATUS_ACTIVE),
            'position' => $this->integer()->defaultValue(0),
            'short_text' => $this->string(80),
            'public_text' => $this->text(),
            'private_text' => $this->text(),
        ], $tableOptions);

        $this->addForeignKey(
            'group_membership_character',
            'group_membership', 'character_id',
            '{{%character}}', 'character_id',
            'RESTRICT', 'CASCADE'
        );
        $this->addForeignKey(
            'group_membership_group',
            'group_membership', 'group_id',
            '{{%group}}', 'group_id',
            'RESTRICT', 'CASCADE'
        );

        /* Membership history */
        $this->createTable('{{%group_membership_history}}', [
            'group_membership_history_id' => $this->primaryKey()->unsigned(),
            'group_membership_id' => $this->integer(11)->unsigned()->notNull(),
            'created_at' => $this->integer(11)->unsigned(),
            'time_ic' => $this->string(),
            'visibility' => $this->string(20)->notNull()->defaultValue(Visibility::VISIBILITY_GM),
            'status' => $this->string(20)->notNull()->defaultValue(\common\models\GroupMembership::STATUS_ACTIVE),
            'short_text' => $this->string(80),
            'public_text' => $this->text(),
            'private_text' => $this->text(),
        ], $tableOptions);

        $this->addForeignKey(
            'group_membership_history_base',
            'group_membership_history', 'group_membership_id',
            'group_membership', 'group_membership_id',
            'RESTRICT', 'CASCADE'
        );

        /* Visibility for story */
        $this->addColumn('story', 'visibility', $this->string(20)->notNull()->defaultValue(Visibility::VISIBILITY_GM)->after('position'));
    }

    public function down()
    {
        $this->dropColumn('character', 'updated_at');

        $this->dropTable('description_history');

        $this->dropTable('scenario');

        $this->dropColumn('group', 'visibility');
        $this->dropForeignKey('group_description_pack', 'group');
        $this->dropColumn('group', 'description_pack_id');

        $this->dropTable('game');

        $this->dropTable('group_membership_history');
        $this->dropTable('group_membership');

        $this->dropColumn('story', 'visibility');
    }
}
