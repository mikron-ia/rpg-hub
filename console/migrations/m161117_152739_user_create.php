<?php

use yii\db\Migration;

/**
 * Class m161117_152739_user_create
 * @todo Adopt into v0.6.0
 */
class m161117_152739_user_create extends Migration
{
    public function up()
    {
        $tableOptions = "CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB";

        $this->createTable('user_invitation', [
            'id' => $this->primaryKey()->unsigned(),
            'email' => $this->string()->notNull(),
            'status' => $this->string()->notNull(),
            'created_by' => $this->integer()->unsigned()->notNull(),
            'created_at' => $this->integer()->notNull(),
            'opened_at' => $this->integer(),
            'used_at' => $this->integer(),
            'revoked_at' => $this->integer(),
            'valid_to' => $this->integer()->notNull(),
            'token' => $this->string()->unique(),
            'message' => $this->text()->notNull(),
            'intended_role' => $this->string(20)->notNull(),
            'note' => $this->string(),
            'language' => $this->char(5)->notNull()->defaultValue('en'),
        ], $tableOptions);

        $this->addForeignKey('user_invitation_user', 'user_invitation', 'created_by', 'user', 'id', 'RESTRICT', 'CASCADE');
    }

    public function down()
    {
        $this->dropForeignKey('user_invitation_user', 'user_invitation');
        $this->dropTable('user_invitation');
    }
}
