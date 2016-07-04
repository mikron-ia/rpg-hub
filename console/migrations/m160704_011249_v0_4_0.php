<?php

use yii\db\Migration;

class m160704_011249_v0_4_0 extends Migration
{
    public function safeUp()
    {
        $this->dropColumn('description_pack', 'name');
        $this->dropColumn('parameter_pack', 'name');
    }

    public function safeDown()
    {
        $this->addColumn('description_pack', 'name', $this->string(80)->notNull());
        $this->addColumn('parameter_pack', 'name',$this->string(80)->notNull());
    }
}
