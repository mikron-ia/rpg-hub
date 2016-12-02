<?php

use yii\db\Migration;

class m161201_234950_movement_log extends Migration
{
    public function up()
    {
        $this->alterColumn('performed_action', 'class', $this->string(80));
        $this->alterColumn('performed_action', 'object_id', $this->integer(11)->unsigned());
    }

    public function down()
    {
        $this->alterColumn('performed_action', 'class', $this->string(80)->notNull());
        $this->alterColumn('performed_action', 'object_id', $this->integer(11)->unsigned()->notNull());
    }
}
