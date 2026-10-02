<?php

use common\models\core\Visibility;
use yii\db\Migration;

class m260627_232740_v1_9_0 extends Migration
{
    public function safeUp(): void
    {
        $this->addColumn('{{%project}}', 'bestowed_list_id', $this->integer(11)->unsigned()->after('based_on_id'));

        $this->addForeignKey(
            'project_bestowed_list',
            '{{%project}}',
            'bestowed_list_id',
            '{{%bestowed_list}}',
            'bestowed_list_id',
            'RESTRICT',
            'CASCADE'
        );

        $this->addColumn('{{%user_invitation}}', 'sent_at', $this->integer(11)->unsigned()->after('created_at'));

        $this->addColumn(
            '{{%recap}}',
            'visibility',
            $this->string(20)->notNull()->defaultValue(Visibility::Full->value)->after('position')
        ); // default is `full` to avoid breaking the existing system and forcing GMs to go through all existing reviews after deployment
    }

    public function safeDown(): void
    {
        $this->dropColumn('{{%recap}}', 'visibility');

        $this->dropColumn('{{%user_invitation}}', 'sent_at');

        $this->dropForeignKey('project_bestowed_list', '{{%project}}');

        $this->dropColumn('{{%project}}', 'bestowed_list_id');
    }
}
