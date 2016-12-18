<?php

use yii\db\Migration;

class m161201_234950_movement_log extends Migration
{
    public function up()
    {
        $this->alterColumn('performed_action', 'class', $this->string(80));
        $this->alterColumn('performed_action', 'object_id', $this->integer(11)->unsigned());
        $this->alterColumn('performed_action', 'user_id', $this->integer(11)->unsigned());

        $tableOptions = "CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB";

        $this->createTable('ip', [
            'id' => $this->primaryKey(),
            'content' => $this->string(40)->notNull(),
        ], $tableOptions);

        $this->createTable('user_agent', [
            'id' => $this->primaryKey(),
            'content' => $this->string()->notNull(),
        ], $tableOptions);

        $this->addColumn('performed_action', 'ip_id', $this->integer(11));
        $this->addColumn('performed_action', 'user_agent_id', $this->integer(11));

        $this->addForeignKey('performed_action_ip', 'performed_action', 'ip_id', 'ip', 'id', 'RESTRICT', 'RESTRICT');
        $this->addForeignKey('performed_action_user_agent', 'performed_action', 'user_agent_id', 'user_agent', 'id', 'RESTRICT', 'RESTRICT');
    }

    public function down()
    {
        $this->alterColumn('performed_action', 'class', $this->string(80)->notNull());
        $this->alterColumn('performed_action', 'object_id', $this->integer(11)->unsigned()->notNull());
        $this->alterColumn('performed_action', 'user_id', $this->integer(11)->unsigned()->notNull());

        $this->dropForeignKey('performed_action_ip', 'performed_action');
        $this->dropForeignKey('performed_action_user_agent', 'performed_action');

        $this->dropColumn('performed_action', 'ip_id');
        $this->dropColumn('performed_action', 'user_agent_id');

        $this->dropTable('ip');
        $this->dropTable('user_agent');
    }
}
