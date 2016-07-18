<?php

require Yii::getAlias('@yii') . '/rbac/migrations/m140506_102106_rbac_init.php';

class m160704_011249_v0_4_0 extends m140506_102106_rbac_init
{
    public function up()
    {
        $this->dropColumn('description_pack', 'name');
        $this->dropColumn('parameter_pack', 'name');

        $scriptName = __DIR__ . '/' . 'data.sql';
        if (file_exists($scriptName)) {
            $scriptContent = file_get_contents($scriptName);
            $this->execute($scriptContent);
        }

        $tableOptions = "CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB";

        $this->createTable('{{%gm}}', [
            'gm_id' => $this->primaryKey()->unsigned(),
            'epic_id' => $this->integer(11)->unsigned()->notNull(),
            'user_id' => $this->integer(11)->unsigned()->notNull(),
        ], $tableOptions);

        $this->createTable('{{%player}}', [
            'player_id' => $this->primaryKey()->unsigned(),
            'epic_id' => $this->integer(11)->unsigned()->notNull(),
            'user_id' => $this->integer(11)->unsigned()->notNull(),
        ], $tableOptions);

        parent::up();
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

        $this->dropTable('{{%gm}}');
        $this->dropTable('{{%player}}');

        $this->execute('SET foreign_key_checks = 1;');

        $this->addColumn('description_pack', 'name', $this->string(80)->notNull());
        $this->addColumn('parameter_pack', 'name', $this->string(80)->notNull());
    }
}
