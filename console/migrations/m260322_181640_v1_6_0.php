<?php

use common\models\Participant;
use common\models\User;
use yii\db\ActiveQuery;
use yii\db\Migration;

class m260322_181640_v1_6_0 extends Migration
{
    /**
     * @throws Exception
     */
    public function safeUp(): void
    {
        $this->addColumn('{{%description}}', 'outdated', $this->boolean()->defaultValue(false)->notNull());

        $this->addColumn('{{%participant}}', 'key', $this->string(80)->after('participant_id'));
        $this->fillInKeys(Participant::find());
        $this->alterColumn('{{%participant}}', 'key', $this->string(80)->notNull());

        $this->addColumn('{{%user}}', 'key', $this->string(80)->after('id'));
        $this->fillInKeys(User::find());
        $this->alterColumn('{{%user}}', 'key', $this->string(80)->notNull());
    }

    public function safeDown(): void
    {
        $this->dropColumn('{{%user}}', 'key');

        $this->dropColumn('{{%participant}}', 'key');

        $this->dropColumn('{{%description}}', 'outdated');
    }

    /**
     * @throws Exception
     */
    private function fillInKeys(ActiveQuery $objects): void
    {
        foreach ($objects->all() as $object) {
            if (empty($object->key)) {
                $object->fillInKey()->save();
            }
        }
    }
}
