<?php

use yii\db\Migration;

class m250908_000840_v1_5_0 extends Migration
{
    public function safeUp(): void
    {
        $tableOptions = "CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB";

        $this->addColumn('parameter_pack', 'parameters_full', $this->json()->defaultValue(json_encode([]))->notNull());
        $this->addColumn('parameter_pack', 'parameters_gm', $this->json()->defaultValue(json_encode([]))->notNull());

        $this->addColumn('scenario', 'content', $this->text()->after('tag_line')->null());
        $this->addColumn('scenario', 'content_expanded', $this->text()->after('content')->null());

        $this->addColumn('story', 'based_on_id', $this->integer(10)->unsigned()->after('visibility')->null());

        $this->addForeignKey(
            'story_based_on_ibfk1',
            'story',
            'based_on_id',
            'scenario',
            'scenario_id',
            'RESTRICT',
            'CASCADE'
        );

        $this->createTable('{{%story_character_assignment}}', [
            'story_character_assignment_id' => $this->primaryKey()->unsigned(),
            'character_id' => $this->integer(11)->unsigned()->notNull(),
            'story_id' => $this->integer(11)->unsigned()->notNull(),
            'visibility' => $this->string(20)->notNull(),
        ], $tableOptions);

        $this->addForeignKey(
            'story_character_assignment_character',
            'story_character_assignment', 'character_id',
            '{{%character}}', 'character_id',
            'RESTRICT', 'CASCADE'
        );
        $this->addForeignKey(
            'story_character_assignment_story',
            'story_character_assignment', 'story_id',
            '{{%story}}', 'story_id',
            'RESTRICT', 'CASCADE'
        );

        $this->createTable('{{%story_group_assignment}}', [
            'story_group_assignment_id' => $this->primaryKey()->unsigned(),
            'group_id' => $this->integer(11)->unsigned()->notNull(),
            'story_id' => $this->integer(11)->unsigned()->notNull(),
            'visibility' => $this->string(20)->notNull(),
        ], $tableOptions);

        $this->addForeignKey(
            'story_group_assignment_group',
            'story_group_assignment', 'group_id',
            '{{%group}}', 'group_id',
            'RESTRICT', 'CASCADE'
        );
        $this->addForeignKey(
            'story_group_assignment_story',
            'story_group_assignment', 'story_id',
            '{{%story}}', 'story_id',
            'RESTRICT', 'CASCADE'
        );
    }

    public function safeDown(): void
    {
        $this->dropForeignKey('story_group_assignment_story', 'story_group_assignment');
        $this->dropForeignKey('story_group_assignment_group', 'story_group_assignment');
        $this->dropTable('{{%story_group_assignment}}');

        $this->dropForeignKey('story_character_assignment_story', 'story_character_assignment');
        $this->dropForeignKey('story_character_assignment_character', 'story_character_assignment');
        $this->dropTable('{{%story_character_assignment}}');

        $this->dropForeignKey('story_based_on_ibfk1', 'story');

        $this->dropColumn('story', 'based_on_id');

        $this->dropColumn('scenario', 'content_expanded');
        $this->dropColumn('scenario', 'content');

        $this->dropColumn('parameter_pack', 'parameters_gm');
        $this->dropColumn('parameter_pack', 'parameters_full');
    }
}
