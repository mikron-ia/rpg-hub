<?php

use yii\db\Migration;

class m250802_225652_v1_4_0 extends Migration
{
    public function safeUp(): void
    {
        $this->addColumn('{{%group}}', 'display_as_tab', $this->boolean()->notNull()->defaultValue(false));
    }

    public function safeDown(): void
    {
        $this->dropColumn('{{%group}}', 'display_as_tab');
    }
}
