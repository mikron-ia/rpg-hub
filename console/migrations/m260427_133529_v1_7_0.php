<?php

use common\models\core\ImportanceCategory;
use common\models\core\Visibility;
use yii\db\Migration;

class m260427_133529_v1_7_0 extends Migration
{
    #[Override]
    public function safeUp(): void
    {
        $this->createTable('{{%location}}', [
            'location_id' => $this->primaryKey()->unsigned(),
            'epic_id' => $this->integer(11)->unsigned()->notNull(),
            'key' => $this->string(80)->notNull(),
            'name' => $this->string(120)->notNull(),
            'tagline' => $this->string(120)->notNull(),
            'visibility' => $this->string(20)->notNull()->defaultValue(Visibility::VISIBILITY_GM->value),
            'importance_category' => $this->string(20)->notNull()->defaultValue(ImportanceCategory::IMPORTANCE_MEDIUM->value),
            'updated_at' => $this->integer(11)->unsigned(),
            'modified_at' => $this->integer(11)->unsigned(),
            'description_pack_id' => $this->integer(11)->unsigned(),
            'importance_pack_id' => $this->integer(11)->unsigned(),
            'scribble_pack_id' => $this->integer(11)->unsigned(),
            'seen_pack_id' => $this->integer(11)->unsigned(),
            'utility_bag_id' => $this->integer(11)->unsigned(),
        ]);
    }

    #[Override]
    public function safeDown(): void
    {
        $this->dropTable('{{%location}}');
    }
}
