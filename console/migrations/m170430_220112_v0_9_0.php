<?php

use yii\db\Migration;

class m170430_220112_v0_9_0 extends Migration
{
    public function up()
    {
        /**
         * Loading up data, if available
         * This is a stopgap measure that should be moved forward to newest migration and removed no later than in 1.0
         * File must contain data that conform to structure established for 0.8.0 and must not contain any data for `migration` table
         */
        $scriptName = __DIR__ . '/' . 'data.sql';
        if (file_exists($scriptName)) {
            $scriptContent = file_get_contents($scriptName);
            $this->execute($scriptContent);
        }
    }

    public function down()
    {
    }

}
