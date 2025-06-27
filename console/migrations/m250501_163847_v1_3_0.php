<?php

use common\models\core\CharacterSheetDataState;
use yii\db\Migration;

class m250501_163847_v1_3_0 extends Migration
{
    public function safeUp(): void
    {
        $tableOptions = "CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB";

        $this->renameColumn('{{%recap}}', 'data', 'content');
        $this->addColumn('{{%recap}}', 'content_expanded', $this->text()->after('content')->defaultValue(null));

        $this->addColumn('{{%description}}', 'point_in_time_still_valid_id', $this->integer()->unsigned());
        $this->addColumn('{{%description_history}}', 'point_in_time_still_valid_id', $this->integer()->unsigned());

        $this->addForeignKey(
            'description_point_in_time_still_valid',
            'description', 'point_in_time_still_valid_id',
            'point_in_time', 'point_in_time_id',
            'RESTRICT', 'CASCADE'
        );

        $this->addForeignKey(
            'description_history_point_in_time_still_valid',
            'description_history', 'point_in_time_still_valid_id',
            'point_in_time', 'point_in_time_id',
            'RESTRICT', 'CASCADE'
        );

        $this->addColumn(
            '{{%character_sheet}}',
            'data_state',
            $this->char(10)->after('data')->defaultValue(CharacterSheetDataState::Incomplete->value),
        );

        $this->createTable('{{%announcement}}', [
            'announcement_id' => $this->primaryKey()->unsigned(),
            'epic_id' => $this->integer()->unsigned(),
            'title' => $this->string(),
            'content' => $this->text(),
            'visible_from' => $this->integer()->unsigned(),
            'visible_to' => $this->integer()->unsigned(),
            'created_by' => $this->integer(10)->notNull()->unsigned(),
            'updated_by' => $this->integer(10)->notNull()->unsigned(),
            'created_at' => $this->integer()->notNull()->unsigned(),
            'updated_at' => $this->integer()->notNull()->unsigned(),
        ], $tableOptions);
    }

    public function safeDown(): void
    {
        $this->dropTable('{{%announcement}}');

        $this->dropColumn('{{%character_sheet}}', 'data_state');

        $this->dropForeignKey('description_history_point_in_time_still_valid', 'description_history');
        $this->dropForeignKey('description_point_in_time_still_valid', 'description');

        $this->dropColumn('{{%description_history}}', 'point_in_time_still_valid_id');
        $this->dropColumn('{{%description}}', 'point_in_time_still_valid_id');

        $this->dropColumn('{{%recap}}', 'content_expanded');
        $this->renameColumn('{{%recap}}', 'content', 'data');
    }
}
