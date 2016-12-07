<?php

use yii\db\Migration;

class m161207_225337_seen_by extends Migration
{
    public function up()
    {
        $tableOptions = "CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB";

        $this->createTable('seen_pack', [
            'seen_pack_id' => $this->primaryKey()->unsigned(),
            'class' => $this->string(20)->notNull()->comment("Name of class this pack belongs to; necessary for proper type assignment"),
            'name' => $this->string(80)->notNull(),
        ], $tableOptions);

        $this->createTable('seen', [
            'seen_id' => $this->primaryKey()->unsigned(),
            'seen_pack_id' => $this->integer(11)->unsigned(),
            'user_id' => $this->integer(10)->unsigned(),
            'noted_at' => $this->integer(11)->unsigned()->comment("Seen in any manner - like in index"),
            'seen_at' => $this->integer(11)->unsigned()->comment("Seen properly - like in view"),
            'status' => $this->string(16)->comment("Quick status to be served in index"),
            'alert_threshold' => $this->smallInteger(),
        ], $tableOptions);

        $this->addForeignKey('seen_pack', '{{%seen}}', 'seen_pack_id', '{{%seen_pack}}', 'seen_pack_id', 'RESTRICT', 'CASCADE');
    }

    public function down()
    {
        $this->dropForeignKey('seen_pack', '{{%seen}}');

        $this->dropTable('seen');
        $this->dropTable('seen_pack');
    }
}
