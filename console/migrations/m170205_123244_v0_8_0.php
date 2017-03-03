<?php

use common\models\core\Visibility;
use yii\db\Migration;

class m170205_123244_v0_8_0 extends Migration
{
    public function up()
    {
        $this->addColumn('character', 'updated_at', $this->integer()->notNull()->after('importance'));
        $this->update('character', ['updated_at' => time()]);

        $tableOptions = "CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB";

        /* Description history mechanism */
        $this->createTable('{{%description_history}}', [
            'description_history_id' => $this->primaryKey()->unsigned(),
            'description_id' => $this->integer(11)->unsigned(),
            'created_at' => $this->integer(11)->unsigned(),
            'public_text' => $this->text()->notNull(),
            'private_text' => $this->text(),
            'visibility' => $this->string(20)->notNull()->defaultValue(Visibility::VISIBILITY_GM),
        ], $tableOptions);

        $this->addForeignKey(
            'description_history_description',
            'description_history',
            'description_id',
            'description',
            'description_id'
        );

        /* Scenarios */
        $this->createTable('{{%scenario}}', [
            'scenario_id' => $this->primaryKey()->unsigned(),
            'epic_id' => $this->integer(11)->unsigned()->notNull(),
            'key' => $this->string(80)->notNull(),
            'name' => $this->string(120)->notNull(),
            'tag_line' => $this->string(),
            'description_pack_id' => $this->integer(11)->unsigned(),
        ], $tableOptions);


        $this->addForeignKey(
            'scenario_epic',
            'scenario', 'epic_id',
            '{{%epic}}', 'epic_id',
            'RESTRICT', 'CASCADE'
        );
        $this->addForeignKey(
            'scenario_description_pack',
            'scenario', 'description_pack_id',
            '{{%description_pack}}', 'description_pack_id',
            'RESTRICT', 'CASCADE'
        );

        /* Visibility & descriptions for groups */
        $this->addColumn('group', 'visibility', $this->string(20)->notNull()->defaultValue(Visibility::VISIBILITY_GM));
        $this->addColumn('group', 'description_pack_id', $this->integer(11)->unsigned());
    }

    public function down()
    {
        $this->dropColumn('character', 'updated_at');

        $this->dropTable('description_history');

        $this->dropTable('scenario');

        $this->dropColumn('group', 'visibility');

        $this->dropColumn('group', 'description_pack_id');
    }
}
