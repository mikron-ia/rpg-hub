<?php

use yii\db\Migration;

class m160602_230625_v0_3_0 extends Migration
{
    public function safeUp()
    {
        $this->alterColumn('person', 'visibility', $this->string(20));
    }

    public function safeDown()
    {
        $this->alterColumn('person', 'visibility', 'ENUM(\'none\',\'linked\',\'complete\') NULL');
    }
}
