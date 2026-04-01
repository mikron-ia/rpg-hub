<?php

use common\models\GroupMembership;
use common\models\Parameter;
use common\models\ParameterPack;
use common\models\Participant;
use common\models\PointInTime;
use common\models\User;
use common\models\UserInvitation;
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
        $this->createIndex('participant_key', '{{%participant}}', 'key', true);

        $this->addColumn('{{%user}}', 'key', $this->string(80)->after('id'));
        $this->fillInKeys(User::find());
        $this->alterColumn('{{%user}}', 'key', $this->string(80)->notNull());
        $this->createIndex('user_key', '{{%user}}', 'key', true);

        $this->addColumn('{{%user_invitation}}', 'key', $this->string(80)->after('id'));
        $this->fillInKeys(UserInvitation::find());
        $this->alterColumn('{{%user_invitation}}', 'key', $this->string(80)->notNull());
        $this->createIndex('user_invitation_key', '{{%user_invitation}}', 'key', true);

        $this->addColumn('{{%group_membership}}', 'key', $this->string(80)->after('group_membership_id'));
        $this->fillInKeys(GroupMembership::find());
        $this->alterColumn('{{%group_membership}}', 'key', $this->string(80)->notNull());
        $this->createIndex('group_membership_key', '{{%group_membership}}', 'key', true);

        $this->addColumn('{{%point_in_time}}', 'key', $this->string(80)->after('point_in_time_id'));
        $this->fillInKeys(PointInTime::find());
        $this->alterColumn('{{%point_in_time}}', 'key', $this->string(80)->notNull());
        $this->createIndex('point_in_time_key', '{{%point_in_time}}', 'key', true);

        $this->addColumn('{{%parameter}}', 'key', $this->string(80)->after('parameter_pack_id'));
        $this->fillInKeys(Parameter::find());
        $this->alterColumn('{{%parameter}}', 'key', $this->string(80)->notNull());
        $this->createIndex('parameter_key', '{{%parameter}}', 'key', true);

        $this->addColumn('{{%parameter_pack}}', 'key', $this->string(80)->after('parameter_pack_id'));
        $this->fillInKeys(ParameterPack::find());
        $this->alterColumn('{{%parameter_pack}}', 'key', $this->string(80)->notNull());
        $this->createIndex('parameter_pack_key', '{{%parameter_pack}}', 'key', true);
    }

    public function safeDown(): void
    {
        $this->dropColumn('{{%parameter_pack}}', 'key');

        $this->dropColumn('{{%parameter}}', 'key');

        $this->dropColumn('{{%point_in_time}}', 'key');

        $this->dropColumn('{{%group_membership}}', 'key');

        $this->dropColumn('{{%user_invitation}}', 'key');

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
                $object->detachBehaviors();
                $object->fillInKey()->save(false);
            }
        }
    }
}
