<?php

use common\models\Participant;
use yii\db\Migration;

class m260322_181640_v1_6_0 extends Migration
{
    /**
     * @throws Exception
     */
    public function safeUp(): void
    {
        $this->addColumn('{{%description}}', 'outdated', $this->boolean()->defaultValue(false)->notNull());

        $this->addColumn('{{%participant}}', 'key', $this->string(80)->after('participant_id')); // nullable for now
        $this->fillParticipantKeys();
        $this->alterColumn('{{%participant}}', 'key', $this->string(80)->notNull());
    }

    public function safeDown(): void
    {
        $this->dropColumn('{{%participant}}', 'key');

        $this->dropColumn('{{%description}}', 'outdated');
    }

    /**
     * @throws Exception
     */
    private function fillParticipantKeys(): void
    {
        foreach (Participant::find()->all() as $participant) {
            if (empty($participant->key)) {
                $participant->fillInKey()->save();
            }
        }
    }
}
