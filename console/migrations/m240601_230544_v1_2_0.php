<?php

use yii\db\Migration;

/**
 * Class m240601_230544_v1_2_0
 */
class m240601_230544_v1_2_0 extends Migration
{
    public function safeUp()
    {
        // modified_at separated from updated_at - #399
        $this->addColumn('{{%character}}', 'modified_at', $this->integer()->notNull()->after('updated_at'));
        $this->addColumn('{{%group}}', 'modified_at', $this->integer()->notNull()->after('updated_at'));

        $this->execute('UPDATE `character` SET modified_at = updated_at;');
        $this->execute('UPDATE `group` SET modified_at = updated_at;');
    }

    public function safeDown()
    {
        // modified_at separated from updated_at - #399
        $this->dropColumn('{{%group}}', 'modified_at');
        $this->dropColumn('{{%character}}', 'modified_at');
    }
}
