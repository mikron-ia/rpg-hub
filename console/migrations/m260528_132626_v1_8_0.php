<?php

use common\models\core\Visibility;
use yii\db\Migration;

class m260528_132626_v1_8_0 extends Migration
{
    #[Override]
    public function safeUp(): void
    {
        $tableOptions = "CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB";

        $this->createTable('{{%project}}', [
            'project_id' => $this->primaryKey()->unsigned(),
            'epic_id' => $this->integer(11)->unsigned()->notNull(),
            'key' => $this->string(80)->notNull(),
            'name' => $this->string(120)->notNull(),
            'code' => $this->string(20)->notNull(),
            'status' => $this->string(20)->notNull(),
            'short' => $this->text()->notNull(),
            'long' => $this->text(),
            'notes' => $this->text(),
            'short_expanded' => $this->text(),
            'long_expanded' => $this->text(),
            'notes_expanded' => $this->text(),
            'position' => $this->integer()->defaultValue(0),
            'visibility' => $this->string(20)->notNull()->defaultValue(Visibility::VISIBILITY_GM->value),
            'data' => $this->text(),
            'based_on_id' => $this->integer(10)->unsigned(),
            'parameter_pack_id' => $this->integer(11)->unsigned(),
            'seen_pack_id' => $this->integer(11)->unsigned(),
            'FOREIGN KEY (based_on_id) REFERENCES `scenario` (scenario_id) ON DELETE RESTRICT ON UPDATE CASCADE',
            'FOREIGN KEY (parameter_pack_id) REFERENCES `parameter_pack` (parameter_pack_id) ON DELETE RESTRICT ON UPDATE CASCADE',
            'FOREIGN KEY (seen_pack_id) REFERENCES `seen_pack` (seen_pack_id) ON DELETE RESTRICT ON UPDATE CASCADE',
            'FOREIGN KEY (epic_id) REFERENCES `epic` (epic_id) ON DELETE RESTRICT ON UPDATE CASCADE',
        ], $tableOptions);

        $this->createTable('{{%bestowed_list}}', [
            'bestowed_list_id' => $this->primaryKey()->unsigned(),
            'key' => $this->string(80)->notNull(),
            'created_at' => $this->integer(11)->unsigned()->notNull(),
            'updated_at' => $this->integer(11)->unsigned()->notNull(),
        ]);

        $this->createTable('{{%bestowed}}', [
            'bestowed_id' => $this->primaryKey()->unsigned(),
            'bestowed_list_id' => $this->integer(11)->unsigned()->notNull(),
            'user_id' => $this->integer(11)->unsigned()->notNull(),
            'created_at' => $this->integer(11)->unsigned()->notNull(),
            'updated_at' => $this->integer(11)->unsigned()->notNull(),
            'FOREIGN KEY (bestowed_list_id) REFERENCES `bestowed_list` (bestowed_list_id) ON DELETE RESTRICT ON UPDATE CASCADE',
        ]);

        $this->createTable('{{%secret}}', [
            'secret_id' => $this->primaryKey()->unsigned(),
            'epic_id' => $this->integer(11)->unsigned()->notNull(),
            'key' => $this->string(80)->notNull(),
            'title' => $this->string(120)->notNull(),
            'content' => $this->text()->notNull(),
            'notes' => $this->text(),
            'content_expanded' => $this->text(),
            'notes_expanded' => $this->text(),
            'bestowed_list_id' => $this->integer(11)->unsigned(),
            'created_at' => $this->integer(11)->unsigned()->notNull(),
            'updated_at' => $this->integer(11)->unsigned()->notNull(),
            'FOREIGN KEY (bestowed_list_id) REFERENCES `bestowed_list` (bestowed_list_id) ON DELETE RESTRICT ON UPDATE CASCADE',
        ]);
    }

    #[Override]
    public function safeDown(): void
    {
        $this->dropTable('{{%project}}');
    }
}
