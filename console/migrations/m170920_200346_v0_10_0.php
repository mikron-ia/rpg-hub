<?php

use yii\db\Migration;

class m170920_200346_v0_10_0 extends Migration
{
    public function safeUp()
    {
        /* Story typing */
        $this->addColumn('story', 'code', $this->string(40)->notNull()->after('visibility'));

        /* Protected descriptions */
        $this->addColumn('description', 'protected_text', $this->text()->after('public_text'));

        /* Flag pack */
        /* @todo Create the table */
        /* @todo Attach to tables of flaggable objects - character and group */

        /* System of TO DO records for players */
        /* @todo Create the table */
    }

    public function safeDown()
    {
        $this->dropColumn('story', 'code');
        $this->dropColumn('description', 'protected_text');
    }
}
