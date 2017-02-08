<?php

use yii\db\Migration;

class m170205_123244_v0_8_0 extends Migration
{
    public function up()
    {
        $this->addColumn('character', 'updated_at', $this->integer()->notNull()->after('importance'));
    }

    public function down()
    {
        $this->dropColumn('character', 'updated_at');
    }
}
