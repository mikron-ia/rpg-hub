<?php

use yii\db\Migration;

/**
 * Class m190108_142043_status_on_epic
 */
class m190108_142043_status_on_epic extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%epic}}', 'status', $this->string(20)->notNull()->after('system')->defaultValue(\common\models\Epic::STATUS_PROPOSED));
    }

    public function safeDown()
    {
        $this->dropColumn('{{%epic}}', 'status');
    }
}
