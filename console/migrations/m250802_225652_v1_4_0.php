<?php

use yii\db\Migration;

class m250802_225652_v1_4_0 extends Migration
{
    public function safeUp(): void
    {
        $this->addColumn('{{%group}}', 'display_as_tab', $this->boolean()->notNull()->defaultValue(false));

        $this->addColumn('{{%story}}', 'short_expanded', $this->text()->after('long'));
        $this->addColumn('{{%story}}', 'long_expanded', $this->text()->after('short_expanded'));
    }

    public function safeDown(): void
    {
        $this->dropColumn('{{%group}}', 'display_as_tab');

        $this->dropColumn('{{%story}}', 'long_expanded');
        $this->dropColumn('{{%story}}', 'short_expanded');
    }
}
