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

        /**
         * Activity log table
         */
        $this->createTable('{{%user_action}}', [
            'user_action_id' => $this->primaryKey()->unsigned(),
            'user_id' => $this->integer(11)->unsigned()->notNull(),
            'operation' => $this->string(80)->notNull(),
            'class' => $this->string(80)->notNull(),
            'id' => $this->integer(11)->unsigned()->notNull(),
            'FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE RESTRICT ON UPDATE CASCADE',
        ], $tableOptions);

        parent::up();

        /**
         * Loading up data, if available
         * This is a stopgap measure that should be moved forward to newest migration and removed no later than in 1.0
         */
        $scriptName = __DIR__ . '/' . 'data.sql';
        if (file_exists($scriptName)) {
            $scriptContent = file_get_contents($scriptName);
            $this->execute($scriptContent);
        }
    }

    public function down()
    {
        parent::down();

        $this->execute('SET foreign_key_checks = 0;');

        $this->truncateTable('{{%character}}');
        $this->truncateTable('{{%description}}');
        $this->truncateTable('{{%description_pack}}');
        $this->truncateTable('{{%epic}}');
        $this->truncateTable('{{%group}}');
        $this->truncateTable('{{%parameter}}');
        $this->truncateTable('{{%parameter_pack}}');
        $this->truncateTable('{{%person}}');
        $this->truncateTable('{{%recap}}');
        $this->truncateTable('{{%story}}');
        $this->truncateTable('{{%story_parameter}}');
        $this->truncateTable('{{%user}}');

        $this->dropTable('{{%participant}}');
        $this->dropTable('{{%participant_role}}');

        $this->dropTable('{{%user_action}}');

        $this->addColumn('description_pack', 'name', $this->string(80)->notNull());
        $this->addColumn('parameter_pack', 'name', $this->string(80)->notNull());

        $this->execute('SET foreign_key_checks = 1;');
    }
}
