<?php

require Yii::getAlias('@yii') . '/rbac/migrations/m140506_102106_rbac_init.php';

class m160704_011249_v0_4_0 extends m140506_102106_rbac_init
{
    public function up()
    {
        $this->dropColumn('description_pack', 'name');
        $this->dropColumn('parameter_pack', 'name');

        $tableOptions = "CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB";

        $this->createTable('{{%participant}}', [
            'participant_id' => $this->primaryKey()->unsigned(),
            'user_id' => $this->integer(11)->unsigned()->notNull(),
            'epic_id' => $this->integer(11)->unsigned()->notNull(),
            'FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE RESTRICT ON UPDATE CASCADE',
            'FOREIGN KEY (epic_id) REFERENCES `epic` (epic_id) ON DELETE RESTRICT ON UPDATE CASCADE',
        ], $tableOptions);

        $this->createIndex('participant_unique', 'participant', ['user_id', 'epic_id'], true);

        $this->createTable('{{%participant_role}}', [
            'participant_id' => $this->integer(11)->unsigned()->notNull(),
            'role' => $this->string(20)->notNull(),
            'PRIMARY KEY (participant_id, role)',
            'FOREIGN KEY (participant_id) REFERENCES `participant` (participant_id) ON DELETE RESTRICT ON UPDATE CASCADE',
        ], $tableOptions);

        $this->createTable('{{%performed_action}}', [
            'id' => $this->primaryKey()->unsigned(),
            'user_id' => $this->integer(11)->unsigned()->notNull(),
            'operation' => $this->string(80)->notNull(),
            'class' => $this->string(80)->notNull(),
            'object_id' => $this->integer(11)->unsigned()->notNull(),
            'performed_at' => $this->integer()->notNull(),
            'FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE RESTRICT ON UPDATE CASCADE',
        ], $tableOptions);

        $this->addColumn('character', 'player_id', $this->integer(10)->unsigned());

        $this->execute('SET foreign_key_checks = 0');
        $this->addForeignKey('character_ibfk_3', '{{%character}}', 'player_id', '{{%user}}', 'id', 'RESTRICT', 'CASCADE');
        $this->execute('SET foreign_key_checks = 1');

        parent::up();
    }

    public function down()
    {
        parent::down();

        $this->execute('SET foreign_key_checks = 0;');

        $this->dropTable('{{%participant}}');
        $this->dropTable('{{%participant_role}}');

        $this->dropTable('{{%performed_action}}');

        $this->addColumn('description_pack', 'name', $this->string(80)->notNull());
        $this->addColumn('parameter_pack', 'name', $this->string(80)->notNull());

        $this->dropForeignKey('character_ibfk_3', '{{%character}}');

        $this->execute('SET foreign_key_checks = 1;');
    }
}
