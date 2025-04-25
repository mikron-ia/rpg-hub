<?php

use common\models\Flag;
use yii\db\Migration;

/**
 * Class m240601_230544_v1_2_0
 */
class m240601_230544_v1_2_0 extends Migration
{
    public function safeUp(): void
    {
        // modified_at separated from updated_at - #399
        $this->addColumn('{{%character}}', 'modified_at', $this->integer()->notNull()->after('updated_at'));
        $this->addColumn('{{%group}}', 'modified_at', $this->integer()->notNull()->after('updated_at'));

        $this->execute('UPDATE `character` SET modified_at = updated_at;');
        $this->execute('UPDATE `group` SET modified_at = updated_at;');

        // move recalculation flag to ImportancePack - #403
        // CAUTION: the old flag values are not transferred - it is advised to run the forced full recalculation after deployment
        $this->addColumn(
            '{{%importance_pack}}',
            'flagged',
            $this->boolean()->notNull()->defaultValue(false)->comment('Is flagged for recalculation?')
        );

        // remove old UtilityBag flags from database - #404
        Flag::deleteAll(['type' => Flag::TYPE_CHANGED]);
        Flag::deleteAll(['type' => Flag::TYPE_IMPORTANCE_RECALCULATE]);

        // Improvements to Article - #387
        $this->addColumn('{{%article}}', 'outline_raw', $this->text()->after('position'));
        $this->addColumn('{{%article}}', 'outline_ready', $this->text()->after('outline_raw'));

        // Current story - #371
        $this->addColumn('{{%epic}}', 'current_story_id', $this->integer(10)->unsigned()->after('status'));

        $this->addForeignKey(
            'current_story_ibfk_1',
            '{{%epic}}',
            'current_story_id',
            '{{%story}}',
            'story_id',
            'RESTRICT',
            'CASCADE'
        );
    }

    public function safeDown(): void
    {
        // Current story - #371
        $this->dropForeignKey('current_story_ibfk_1', '{{%epic}}');

        $this->dropColumn('{{%epic}}', 'current_story_id');

        // Improvements to Article - #387
        $this->dropColumn('{{%article}}', 'outline_ready');
        $this->dropColumn('{{%article}}', 'outline_raw');

        // remove UtilityBag flags from database - #404 - is not reversible

        // move recalculation flag to ImportancePack - #403
        $this->dropColumn('{{%importance_pack}}', 'flagged');

        // modified_at separated from updated_at - #399
        $this->dropColumn('{{%group}}', 'modified_at');
        $this->dropColumn('{{%character}}', 'modified_at');
    }
}
