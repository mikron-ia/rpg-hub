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
    }

    public function down()
    {
        $this->dropColumn('character', 'updated_at');

        $this->dropTable('description_history');
    }
}
