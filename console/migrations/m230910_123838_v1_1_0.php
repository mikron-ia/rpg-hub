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
    }

    public function safeDown()
    {
        /* Group updated_at removed */
        $this->dropColumn('{{%group}}', 'updated_at');

        /* Game location removed */
        $this->dropColumn('{{%game}}', 'planned_location');
    }
}
