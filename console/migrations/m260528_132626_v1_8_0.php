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
    }

    #[Override]
    public function safeDown(): void
    {
        $this->dropTable('{{%project}}');
    }
}
