<?php

use yii\db\Migration;

class m161207_225337_seen_by extends Migration
{
    public function up()
    {
        $tableOptions = "CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB";

        $this->createTable('seen_pack', [
            'seen_pack_id' => $this->primaryKey()->unsigned(),
            'class' => $this->string(20)->notNull()->comment("Name of class this pack belongs to; necessary for proper type assignment"),
        ], $tableOptions);

        $this->createTable('seen', [
            'seen_id' => $this->primaryKey()->unsigned(),
            'seen_pack_id' => $this->integer(11)->unsigned(),
            'user_id' => $this->integer(10)->unsigned(),
            'noted_at' => $this->integer(11)->unsigned()->comment("Seen in any manner - like in index"),
            'seen_at' => $this->integer(11)->unsigned()->comment("Seen properly - like in view"),
            'status' => $this->string(16)->comment("Quick status to be served in index"),
            'alert_threshold' => $this->smallInteger(),
        ], $tableOptions);

        $this->addForeignKey('seen_pack', 'seen', 'seen_pack_id', 'seen_pack', 'seen_pack_id', 'RESTRICT', 'CASCADE');
        $this->addForeignKey('user', 'seen', 'user_id', 'user', 'id', 'RESTRICT', 'CASCADE');

        $this->addColumn('character', 'seen_pack_id', $this->integer(11)->unsigned());
        $this->addColumn('character_sheet', 'seen_pack_id', $this->integer(11)->unsigned());
        $this->addColumn('epic', 'seen_pack_id', $this->integer(11)->unsigned());
        $this->addColumn('group', 'seen_pack_id', $this->integer(11)->unsigned());
        $this->addColumn('recap', 'seen_pack_id', $this->integer(11)->unsigned());
        $this->addColumn('story', 'seen_pack_id', $this->integer(11)->unsigned());

        $this->addForeignKey('character_seen', 'character', 'seen_pack_id', 'seen_pack', 'seen_pack_id', 'RESTRICT', 'CASCADE');
        $this->addForeignKey('character_sheet_seen', 'character_sheet', 'seen_pack_id', 'seen_pack', 'seen_pack_id', 'RESTRICT', 'CASCADE');
        $this->addForeignKey('epic_seen', 'epic', 'seen_pack_id', 'seen_pack', 'seen_pack_id', 'RESTRICT', 'CASCADE');
        $this->addForeignKey('group_seen', 'group', 'seen_pack_id', 'seen_pack', 'seen_pack_id', 'RESTRICT', 'CASCADE');
        $this->addForeignKey('recap_seen', 'recap', 'seen_pack_id', 'seen_pack', 'seen_pack_id', 'RESTRICT', 'CASCADE');
        $this->addForeignKey('story_seen', 'story', 'seen_pack_id', 'seen_pack', 'seen_pack_id', 'RESTRICT', 'CASCADE');
    }

    public function down()
    {
        $this->dropForeignKey('seen_pack', '{{%seen}}');
        $this->dropForeignKey('user', '{{%seen}}');

        $this->dropForeignKey('character_seen', '{{%character}}');
        $this->dropForeignKey('character_sheet_seen', '{{%character_sheet}}');
        $this->dropForeignKey('epic_seen', '{{%epic}}');
        $this->dropForeignKey('group_seen', '{{%group}}');
        $this->dropForeignKey('recap_seen', '{{%recap}}');
        $this->dropForeignKey('story_seen', '{{%story}}');

        $this->dropColumn('character', 'seen_pack_id');
        $this->dropColumn('character_sheet', 'seen_pack_id');
        $this->dropColumn('epic', 'seen_pack_id');
        $this->dropColumn('group', 'seen_pack_id');
        $this->dropColumn('recap', 'seen_pack_id');
        $this->dropColumn('story', 'seen_pack_id');

        $this->dropTable('seen');
        $this->dropTable('seen_pack');
    }
}
