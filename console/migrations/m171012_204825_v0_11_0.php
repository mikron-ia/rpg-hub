<?php

use yii\db\Migration;

class m171012_204825_v0_11_0 extends Migration
{
    public function safeUp()
    {
        $this->addColumn('description', 'public_text_expanded', $this->text()->after('private_text'));
        $this->addColumn('description', 'protected_text_expanded', $this->text()->after('public_text_expanded'));
        $this->addColumn('description', 'private_text_expanded', $this->text()->after('protected_text_expanded'));

        $this->addColumn('description_history', 'public_text_expanded', $this->text()->after('private_text'));
        $this->addColumn('description_history', 'protected_text_expanded', $this->text()->after('public_text_expanded'));
        $this->addColumn('description_history', 'private_text_expanded', $this->text()->after('protected_text_expanded'));

        $this->execute('UPDATE description SET public_text_expanded = public_text');
        $this->execute('UPDATE description SET protected_text_expanded = protected_text');
        $this->execute('UPDATE description SET private_text_expanded = private_text');

        $this->execute('UPDATE description_history SET public_text_expanded = public_text');
        $this->execute('UPDATE description_history SET protected_text_expanded = protected_text');
        $this->execute('UPDATE description_history SET private_text_expanded = private_text');
    }

    public function safeDown()
    {
        $this->dropColumn('description_history', 'public_text_expanded');
        $this->dropColumn('description_history', 'protected_text_expanded');
        $this->dropColumn('description_history', 'private_text_expanded');

        $this->dropColumn('description', 'public_text_expanded');
        $this->dropColumn('description', 'protected_text_expanded');
        $this->dropColumn('description', 'private_text_expanded');
    }
}
