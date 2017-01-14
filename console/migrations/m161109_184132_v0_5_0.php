<?php

use yii\db\Migration;

class m161109_184132_v0_5_0 extends Migration
{
    public function up()
    {
        $tableOptions = "CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB";

        /* External data tables */
        $this->createTable('{{%external_data_pack}}', [
            'external_data_pack_id' => $this->primaryKey()->unsigned(),
            'class' => $this->string(20)->notNull()->comment("Name of class this pack belongs to; necessary for proper type assignment"),
        ], $tableOptions);

        $this->createTable('{{%external_data}}', [
            'external_data_id' => $this->primaryKey()->unsigned(),
            'external_data_pack_id' => $this->integer(11)->unsigned(),
            'code' => $this->string(40)->notNull(),
            'data' => $this->text()->notNull(),
            'visibility' => $this->string(20)->notNull()->defaultValue(\common\models\core\Visibility::VISIBILITY_GM),
        ], $tableOptions);

        $this->addColumn('person', 'external_data_pack_id', $this->integer(11)->unsigned());

        $this->addForeignKey('external_data_pack_ibfk_1', '{{%external_data}}', 'external_data_pack_id', '{{%external_data_pack}}', 'external_data_pack_id', 'RESTRICT', 'CASCADE');
        $this->addForeignKey('person_ibfk_4', '{{%person}}', 'external_data_pack_id', '{{%external_data_pack}}', 'external_data_pack_id', 'RESTRICT', 'CASCADE');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0;');

        $this->dropForeignKey('person_ibfk_4', '{{%person}}');

        $this->dropColumn('person', 'external_data_pack_id');

        $this->dropTable('{{%external_data}}');
        $this->dropTable('{{%external_data_pack}}');

        $this->execute('SET foreign_key_checks = 1;');
    }
}
