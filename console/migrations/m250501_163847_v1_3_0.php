<?php

use yii\db\Migration;

class m250501_163847_v1_3_0 extends Migration
{
    public function safeUp(): void
    {
        $this->renameColumn('{{%recap}}', 'data', 'content');
        $this->addColumn('{{%recap}}', 'content_expanded', $this->text()->after('content')->defaultValue(null));
    }

    public function safeDown(): void
    {
        $this->dropColumn('{{%recap}}', 'content_expanded');
        $this->renameColumn('{{%recap}}', 'content', 'data');
    }
}
