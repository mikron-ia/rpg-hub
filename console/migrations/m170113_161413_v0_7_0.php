<?php

use common\models\core\ImportanceCategory;
use yii\db\Migration;

class m170113_161413_v0_7_0 extends Migration
{
    public function up()
    {
        $this->addColumn(
            'character',
            'importance',
            $this->string(20)->notNull()->defaultValue(ImportanceCategory::IMPORTANCE_MEDIUM->value)->after('visibility')
        );

        $this->addColumn('recap', 'position', $this->integer()->unsigned());
        $this->alterColumn('recap', 'time', $this->string());
    }

    public function down()
    {
        $this->dropColumn('character', 'importance');

        $this->dropColumn('recap', 'position');
        $this->alterColumn('recap', 'time', $this->dateTime()->notNull());
    }
}
