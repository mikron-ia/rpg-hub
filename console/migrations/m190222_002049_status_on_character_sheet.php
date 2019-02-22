<?php

use yii\db\Migration;

/**
 * Class m190222_102049_status_on_character_sheet
 */
class m190222_002049_status_on_character_sheet extends Migration
{
    public function safeUp()
    {
        $this->addColumn(
            '{{%character_sheet}}',
            'status',
            $this->string(20)->notNull()->after('currently_delivered_character_id')->defaultValue(\common\models\CharacterSheet::STATUS_DRAFT)
        );
    }

    public function safeDown()
    {
        $this->dropColumn('{{%character_sheet}}', 'status');
    }
}
