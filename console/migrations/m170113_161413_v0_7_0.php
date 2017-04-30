<?php

use common\models\core\Importance;
use yii\db\Migration;

class m170113_161413_v0_7_0 extends Migration
{
    public function up()
    {
        $this->addColumn('character', 'importance', $this->string(20)->notNull()->defaultValue(Importance::IMPORTANCE_MEDIUM)->after('visibility'));

        $this->addColumn('recap', 'position', $this->integer()->unsigned());
        $this->alterColumn('recap', 'time', $this->string());
    }

    public function down()
    {
        $this->dropColumn('character', 'importance');

        $this->dropColumn('recap', 'position');
        $this->alterColumn('recap', 'time', $this->dateTime()->notNull());

        $this->execute('SET foreign_key_checks = 0;');

        $this->truncateTable('{{%auth_assignment}}');
        $this->truncateTable('{{%auth_item}}');
        $this->truncateTable('{{%auth_item_child}}');
        $this->truncateTable('{{%auth_rule}}');
        $this->truncateTable('{{%character}}');
        $this->truncateTable('{{%character_sheet}}');
        $this->truncateTable('{{%description}}');
        $this->truncateTable('{{%description_pack}}');
        $this->truncateTable('{{%epic}}');
        $this->truncateTable('{{%external_data}}');
        $this->truncateTable('{{%external_data_pack}}');
        $this->truncateTable('{{%group}}');
        $this->truncateTable('{{%ip}}');
        $this->truncateTable('{{%parameter}}');
        $this->truncateTable('{{%parameter_pack}}');
        $this->truncateTable('{{%participant}}');
        $this->truncateTable('{{%participant_role}}');
        $this->truncateTable('{{%performed_action}}');
        $this->truncateTable('{{%recap}}');
        $this->truncateTable('{{%seen}}');
        $this->truncateTable('{{%seen_pack}}');
        $this->truncateTable('{{%story}}');
        $this->truncateTable('{{%story_parameter}}');
        $this->truncateTable('{{%user}}');
        $this->truncateTable('{{%user_agent}}');
        $this->truncateTable('{{%user_invitation}}');

        $this->execute('SET foreign_key_checks = 1;');
    }
}
