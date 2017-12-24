<?php

use common\models\PointInTime;
use yii\db\Migration;

class m171012_204825_v0_11_0 extends Migration
{
    public function safeUp()
    {
        $tableOptions = "CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB";

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

        /* Utility packs */
        $this->createTable('utility_bag', [
            'utility_bag_id' => $this->primaryKey()->unsigned(),
            'class' => $this->string(20)->notNull()->comment("Name of class this pack belongs to; necessary for proper type assignment"),
        ], $tableOptions);
    }

    public function safeDown()
    {
        $this->dropTable('utility_bag');

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
