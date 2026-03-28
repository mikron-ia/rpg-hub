<?php

use common\models\Participant;
use common\models\User;
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
        $this->fillParticipantKeys();
        $this->alterColumn('{{%participant}}', 'key', $this->string(80)->notNull());

        $this->addColumn('{{%user}}', 'key', $this->string(80)->after('id'));
        $this->fillUserKeys();
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
    private function fillParticipantKeys(): void
    {
        foreach (Participant::find()->all() as $participant) {
            if (empty($participant->key)) {
                $participant->fillInKey(Participant::keyParameterName())->save();
            }
        }
    }

    /**
     * @throws Exception
     */
    private function fillUserKeys(): void
    {
        foreach (User::find()->all() as $user) {
            if (empty($user->key)) {
                $user->fillInKey(User::keyParameterName())->save();
            }
        }
    }
}
