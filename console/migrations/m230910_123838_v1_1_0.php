<?php

use yii\db\Migration;

/**
 * Class m230910_123838_v1_1_0
 */
class m230910_123838_v1_1_0 extends Migration
{
    public function safeUp()
    {
        $tableOptions = "CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB";

        /* Group updated_at added */
        $this->addColumn(
            '{{%group}}',
            'updated_at',
            $this->integer()->notNull()->after('importance_category')
        );
        $this->update(
            '{{%group}}',
            ['updated_at' => time()]
        );

        /* Session location added */
        $this->addColumn('{{%game}}', 'planned_location', $this->string(80)->after('planned_date'));

        /* Scribbles created */
        $this->createTable(
            '{{%scribble_pack}}',
            [
                'scribble_pack_id' => $this->primaryKey()->unsigned(),
                'class' => $this->string(20)->notNull()->comment("Name of class this pack belongs to; necessary for proper type assignment"),
            ],
            $tableOptions
        );

        $this->createTable(
            '{{%scribble}}',
            [
                'scribble_id' => $this->primaryKey()->unsigned(),
                'scribble_pack_id' => $this->integer(11)->unsigned(),
                'user_id' => $this->integer(10)->unsigned(),
                'favorite' => $this->boolean(),
            ],
            $tableOptions
        );

        $this->addForeignKey(
            'scribble_pack',
            '{{%scribble}}',
            'scribble_pack_id',
            '{{%scribble_pack}}',
            'scribble_pack_id',
            'RESTRICT',
            'CASCADE'
        );
        $this->addForeignKey(
            'scribble_user',
            '{{%scribble}}',
            'user_id',
            '{{%user}}',
            'id',
            'RESTRICT',
            'CASCADE'
        );

        $this->addColumn(
            '{{%character}}',
            'scribble_pack_id',
            $this->integer(11)->unsigned()->after('importance_pack_id')
        );
        $this->addColumn(
            '{{%group}}',
            'scribble_pack_id',
            $this->integer(11)->unsigned()->after('importance_pack_id')
        );

        $this->addForeignKey(
            'character_scribble_pack',
            '{{%character}}',
            'scribble_pack_id',
            '{{%scribble_pack}}',
            'scribble_pack_id',
            'RESTRICT',
            'CASCADE'
        );
        $this->addForeignKey(
            'group_scribble_pack',
            '{{%group}}',
            'scribble_pack_id',
            '{{%scribble_pack}}',
            'scribble_pack_id',
            'RESTRICT',
            'CASCADE'
        );

        /* Epic styling */
        $this->addColumn('{{%epic}}', 'style', $this->string(20)->after('system'));
    }

    public function safeDown()
    {
        /* Epic styling removed */
        $this->dropColumn('{{%epic}}', 'style');

        /* Scribbles removed */
        $this->dropForeignKey('group_scribble_pack', '{{%group}}');
        $this->dropForeignKey('character_scribble_pack', '{{%character}}');

        $this->dropColumn('{{%group}}', 'scribble_pack_id');
        $this->dropColumn('{{%character}}', 'scribble_pack_id');

        $this->dropTable('{{%scribble}}');
        $this->dropTable('{{%scribble_pack}}');

        /* Group updated_at removed */
        $this->dropColumn('{{%group}}', 'updated_at');

        /* Game location removed */
        $this->dropColumn('{{%game}}', 'planned_location');
    }
}
