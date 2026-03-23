<?php

use yii\db\Migration;

class m260322_181640_v1_6_0 extends Migration
{
    public function safeUp(): void
    {
        $this->addColumn('{{%description}}', 'outdated', $this->boolean()->defaultValue(false)->notNull());
    }

    public function safeDown(): void
    {
        $this->dropColumn('{{%description}}', 'outdated');
    }
}
