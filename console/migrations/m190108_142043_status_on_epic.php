<?php

use common\models\state\EpicStatus;
use yii\db\Migration;

class m190108_142043_status_on_epic extends Migration
{
    public function up(): void
    {
        $this->addColumn(
            '{{%epic}}',
            'status',
            $this->string(20)->notNull()->after('system')->defaultValue(EpicStatus::Proposed->value)
        );
    }

    public function down(): void
    {
        $this->dropColumn('{{%epic}}', 'status');
    }
}
