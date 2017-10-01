<?php

use yii\db\Migration;

class m170920_200346_v0_10_0 extends Migration
{
    public function safeUp()
    {
        $tableOptions = "CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB";

        /* Story typing */
        $this->addColumn('story', 'code', $this->string(40)->notNull()->after('visibility'));

        /* Protected descriptions */
        $this->addColumn('description', 'protected_text', $this->text()->after('public_text'));
        $this->addColumn('description_history', 'protected_text', $this->text()->after('public_text'));

        /* System of TO DO records for users */
        $this->createTable('task', [
            'task_id' => $this->primaryKey()->unsigned(),
            'user_id' => $this->integer(10)->unsigned()->notNull(),
            'title' => $this->string(80)->notNull(),
            'status' => $this->string(8)->notNull(),
            'details' => $this->text(),
        ], $tableOptions);

        $this->addForeignKey('task_user', 'task', 'user_id', 'user', 'id', 'RESTRICT', 'CASCADE');
    }

    public function safeDown()
    {
        $this->dropTable('task');

        $this->dropColumn('story', 'code');
        $this->dropColumn('description', 'protected_text');
        $this->dropColumn('description_history', 'protected_text');
    }
}
