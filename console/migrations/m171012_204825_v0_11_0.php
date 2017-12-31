<?php

use common\models\PointInTime;
use yii\db\Migration;

class m171012_204825_v0_11_0 extends Migration
{
    public function safeUp()
    {
        $tableOptions = "CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB";

        /* Utility packs */
        $this->createTable('utility_bag', [
            'utility_bag_id' => $this->primaryKey()->unsigned(),
            'class' => $this->string(20)->notNull()->comment("Name of class this pack belongs to; necessary for proper type assignment"),
        ], $tableOptions);

        $this->addColumn('article', 'utility_bag_id', $this->integer(11)->unsigned());
        $this->addColumn('character', 'utility_bag_id', $this->integer(11)->unsigned());
        $this->addColumn('character_sheet', 'utility_bag_id', $this->integer(11)->unsigned());
        $this->addColumn('epic', 'utility_bag_id', $this->integer(11)->unsigned());
        $this->addColumn('game', 'utility_bag_id', $this->integer(11)->unsigned());
        $this->addColumn('group', 'utility_bag_id', $this->integer(11)->unsigned());
        $this->addColumn('recap', 'utility_bag_id', $this->integer(11)->unsigned());
        $this->addColumn('story', 'utility_bag_id', $this->integer(11)->unsigned());

        $this->addForeignKey('article_utility_bag', '{{%article}}', 'utility_bag_id', '{{%utility_bag}}', 'utility_bag_id', 'RESTRICT', 'CASCADE');
        $this->addForeignKey('character_utility_bag', '{{%character}}', 'utility_bag_id', '{{%utility_bag}}', 'utility_bag_id', 'RESTRICT', 'CASCADE');
        $this->addForeignKey('character_sheet_utility_bag', '{{%character_sheet}}', 'utility_bag_id', '{{%utility_bag}}', 'utility_bag_id', 'RESTRICT', 'CASCADE');
        $this->addForeignKey('epic_utility_bag', '{{%epic}}', 'utility_bag_id', '{{%utility_bag}}', 'utility_bag_id', 'RESTRICT', 'CASCADE');
        $this->addForeignKey('game_utility_bag', '{{%game}}', 'utility_bag_id', '{{%utility_bag}}', 'utility_bag_id', 'RESTRICT', 'CASCADE');
        $this->addForeignKey('group_utility_bag', '{{%group}}', 'utility_bag_id', '{{%utility_bag}}', 'utility_bag_id', 'RESTRICT', 'CASCADE');
        $this->addForeignKey('recap_utility_bag', '{{%recap}}', 'utility_bag_id', '{{%utility_bag}}', 'utility_bag_id', 'RESTRICT', 'CASCADE');
        $this->addForeignKey('story_utility_bag', '{{%story}}', 'utility_bag_id', '{{%utility_bag}}', 'utility_bag_id', 'RESTRICT', 'CASCADE');

        /* Description rework */
        $this->addColumn('description', 'public_text_expanded', $this->text()->after('private_text'));
        $this->addColumn('description', 'protected_text_expanded', $this->text()->after('public_text_expanded'));
        $this->addColumn('description', 'private_text_expanded', $this->text()->after('protected_text_expanded'));

        $this->addColumn('description_history', 'public_text_expanded', $this->text()->after('private_text'));
        $this->addColumn('description_history', 'protected_text_expanded', $this->text()->after('public_text_expanded'));
        $this->addColumn('description_history', 'private_text_expanded', $this->text()->after('protected_text_expanded'));

        $this->execute('UPDATE description SET public_text_expanded = public_text');
        $this->execute('UPDATE description SET protected_text_expanded = protected_text');
        $this->execute('UPDATE description SET private_text_expanded = private_text');

        $this->execute('UPDATE description_history SET public_text_expanded = public_text');
        $this->execute('UPDATE description_history SET protected_text_expanded = protected_text');
        $this->execute('UPDATE description_history SET private_text_expanded = private_text');

        /* Points in time */
        $this->createTable('point_in_time', [
            'point_in_time_id' => $this->primaryKey()->unsigned(),
            'epic_id' => $this->integer()->unsigned()->notNull(),
            'name' => $this->string(120)->notNull(),
            'text_public' => $this->string(255),
            'text_protected' => $this->string(255),
            'text_private' => $this->string(255),
            'status' => $this->string(10)->notNull()->defaultValue(PointInTime::STATUS_ACTIVE),
            'position' => $this->integer()->unsigned()->notNull(),
        ], $tableOptions);

        $this->addForeignKey(
            'point_in_time_epic',
            'point_in_time', 'epic_id',
            'epic', 'epic_id',
            'RESTRICT', 'CASCADE'
        );

        $this->addColumn('recap', 'point_in_time_id', $this->integer()->unsigned()->after('seen_pack_id'));

        $this->addForeignKey(
            'recap_point_in_time',
            'recap', 'point_in_time_id',
            'point_in_time', 'point_in_time_id',
            'RESTRICT', 'CASCADE'
        );

        $this->importPointsInTimeFromRecaps();

        $this->dropColumn('recap', 'time');

        /* Subgroups */
        $this->addColumn('group', 'master_group_id', $this->integer(11)->unsigned());

        $this->addForeignKey('group_master', 'group', 'master_group_id', 'group', 'group_id', 'RESTRICT', 'CASCADE');

        /* Flags */
        $this->createTable('flag', [
            'flag_id' => $this->primaryKey()->unsigned(),
            'utility_bag_id' => $this->integer()->unsigned()->notNull(),
            'type' => $this->string(10)->notNull(),
            'status' => $this->string(10)->notNull(),
        ], $tableOptions);

        $this->addForeignKey('flag_utility_bag', '{{%flag}}', 'utility_bag_id', '{{%utility_bag}}', 'utility_bag_id', 'RESTRICT', 'CASCADE');
    }

    public function safeDown()
    {
        $this->dropTable('flag');

        $this->dropForeignKey('group_master', 'group');

        $this->dropColumn('group', 'master_group_id');

        $this->addColumn('recap', 'time', $this->string()->after('data'));

        $this->restoreRecapTimeFromPointInTime();

        $this->dropForeignKey('recap_point_in_time', 'recap');

        $this->dropColumn('recap', 'point_in_time_id');

        $this->dropTable('point_in_time');

        $this->dropColumn('description_history', 'public_text_expanded');
        $this->dropColumn('description_history', 'protected_text_expanded');
        $this->dropColumn('description_history', 'private_text_expanded');

        $this->dropColumn('description', 'public_text_expanded');
        $this->dropColumn('description', 'protected_text_expanded');
        $this->dropColumn('description', 'private_text_expanded');

        $this->dropForeignKey('story_utility_bag', '{{%story}}');
        $this->dropForeignKey('recap_utility_bag', '{{%recap}}');
        $this->dropForeignKey('group_utility_bag', '{{%group}}');
        $this->dropForeignKey('game_utility_bag', '{{%game}}');
        $this->dropForeignKey('epic_utility_bag', '{{%epic}}');
        $this->dropForeignKey('character_sheet_utility_bag', '{{%character_sheet}}');
        $this->dropForeignKey('character_utility_bag', '{{%character}}');
        $this->dropForeignKey('article_utility_bag', '{{%article}}');

        $this->dropColumn('story', 'utility_bag_id');
        $this->dropColumn('recap', 'utility_bag_id');
        $this->dropColumn('group', 'utility_bag_id');
        $this->dropColumn('game', 'utility_bag_id');
        $this->dropColumn('epic', 'utility_bag_id');
        $this->dropColumn('character_sheet', 'utility_bag_id');
        $this->dropColumn('character', 'utility_bag_id');
        $this->dropColumn('article', 'utility_bag_id');

        $this->dropTable('utility_bag');
    }

    /**
     * Converts `time` fields into PointInTime objects
     */
    private function importPointsInTimeFromRecaps()
    {
        /** @var \common\models\Recap[] $recaps */
        $recaps = \common\models\Recap::find()->all();

        foreach ($recaps as $recap) {
            if ($recap->time) {
                $pointInTime = new PointInTime();

                $pointInTime->epic_id = $recap->epic_id;
                $pointInTime->name = $recap->time;
                $pointInTime->status = PointInTime::STATUS_ACTIVE;

                $pointInTime->save();
                $pointInTime->refresh();

                $recap->point_in_time_id = $pointInTime->point_in_time_id;
                $recap->save();
            }
        }
    }

    /**
     * Converts PointInTime objects into `time` fields
     */
    private function restoreRecapTimeFromPointInTime()
    {
        /** @var \common\models\Recap[] $recaps */
        $recaps = \common\models\Recap::find()->all();

        foreach ($recaps as $recap) {
            if(!empty($recap->point_in_time_id)) {
                $recap->time = $recap->pointInTime->name;
                $recap->save();
            }
        }
    }
}
