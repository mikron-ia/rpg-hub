<?php

use yii\db\Migration;

class m160313_144138_dev_0 extends Migration
{
    public function safeUp()
    {
        $tables = Yii::$app->db->schema->getTableNames();
        $dbType = $this->db->driverName;
        $tableOptions_mysql = "CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB";

        /* MYSQL */
        if (!in_array('character', $tables)) {
            if ($dbType == "mysql") {
                $this->createTable('{{%character}}', [
                    'character_id' => 'INT(10) UNSIGNED NOT NULL AUTO_INCREMENT',
                    0 => 'PRIMARY KEY (`character_id`)',
                    'key' => 'VARCHAR(80) NOT NULL',
                    'name' => 'VARCHAR(120) NOT NULL',
                    'data' => 'TEXT NOT NULL',
                    'person_id' => 'INT(10) UNSIGNED NULL',
                ], $tableOptions_mysql);
            }
        }

        /* MYSQL */
        if (!in_array('description', $tables)) {
            if ($dbType == "mysql") {
                $this->createTable('{{%description}}', [
                    'description_id' => 'INT(10) UNSIGNED NOT NULL AUTO_INCREMENT',
                    0 => 'PRIMARY KEY (`description_id`)',
                    'description_pack_id' => 'INT(10) UNSIGNED NOT NULL',
                    'title' => 'VARCHAR(80) NOT NULL',
                    'code' => 'VARCHAR(40) NOT NULL',
                    'public_text' => 'TEXT NOT NULL',
                    'private_text' => 'TEXT NOT NULL',
                    'lang' => 'VARCHAR(8) NOT NULL',
                ], $tableOptions_mysql);
            }
        }

        /* MYSQL */
        if (!in_array('description_pack', $tables)) {
            if ($dbType == "mysql") {
                $this->createTable('{{%description_pack}}', [
                    'description_pack_id' => 'INT(10) UNSIGNED NOT NULL AUTO_INCREMENT',
                    0 => 'PRIMARY KEY (`description_pack_id`)',
                    'name' => 'VARCHAR(80) NOT NULL',
                ], $tableOptions_mysql);
            }
        }

        /* MYSQL */
        if (!in_array('group', $tables)) {
            if ($dbType == "mysql") {
                $this->createTable('{{%group}}', [
                    'group_id' => 'INT(10) UNSIGNED NOT NULL AUTO_INCREMENT',
                    0 => 'PRIMARY KEY (`group_id`)',
                    'key' => 'VARCHAR(80) NOT NULL',
                    'name' => 'VARCHAR(120) NOT NULL',
                    'data' => 'TEXT NOT NULL',
                ], $tableOptions_mysql);
            }
        }

        /* MYSQL */
        if (!in_array('person', $tables)) {
            if ($dbType == "mysql") {
                $this->createTable('{{%person}}', [
                    'person_id' => 'INT(10) UNSIGNED NOT NULL AUTO_INCREMENT',
                    0 => 'PRIMARY KEY (`person_id`)',
                    'key' => 'VARCHAR(80) NOT NULL',
                    'name' => 'VARCHAR(120) NOT NULL',
                    'tagline' => 'VARCHAR(120) NOT NULL',
                    'data' => 'TEXT NOT NULL',
                    'visibility' => 'ENUM(\'none\',\'linked\',\'complete\') NULL',
                ], $tableOptions_mysql);
            }
        }

        /* MYSQL */
        if (!in_array('recap', $tables)) {
            if ($dbType == "mysql") {
                $this->createTable('{{%recap}}', [
                    'recap_id' => 'INT(10) UNSIGNED NOT NULL AUTO_INCREMENT',
                    0 => 'PRIMARY KEY (`recap_id`)',
                    'key' => 'VARCHAR(80) NOT NULL',
                    'name' => 'VARCHAR(120) NOT NULL',
                    'data' => 'TEXT NOT NULL',
                    'time' => 'DATETIME NOT NULL',
                ], $tableOptions_mysql);
            }
        }

        /* MYSQL */
        if (!in_array('story', $tables)) {
            if ($dbType == "mysql") {
                $this->createTable('{{%story}}', [
                    'story_id' => 'INT(10) UNSIGNED NOT NULL AUTO_INCREMENT',
                    0 => 'PRIMARY KEY (`story_id`)',
                    'key' => 'VARCHAR(80) NOT NULL',
                    'name' => 'VARCHAR(120) NOT NULL',
                    'data' => 'TEXT NOT NULL',
                ], $tableOptions_mysql);
            }
        }

        /* MYSQL */
        if (!in_array('user', $tables)) {
            if ($dbType == "mysql") {
                $this->createTable('{{%user}}', [
                    'id' => 'INT(11) NOT NULL AUTO_INCREMENT',
                    0 => 'PRIMARY KEY (`id`)',
                    'username' => 'VARCHAR(255) NOT NULL',
                    'auth_key' => 'VARCHAR(32) NOT NULL',
                    'password_hash' => 'VARCHAR(255) NOT NULL',
                    'password_reset_token' => 'VARCHAR(255) NULL',
                    'email' => 'VARCHAR(255) NOT NULL',
                    'status' => 'SMALLINT(6) NOT NULL DEFAULT \'10\'',
                    'created_at' => 'INT(11) NOT NULL',
                    'updated_at' => 'INT(11) NOT NULL',
                    'language' => 'CHAR(2) NOT NULL DEFAULT \'en\'',
                ], $tableOptions_mysql);
            }
        }


        $this->createIndex('idx_person_id_6733_00', 'character', 'person_id', 0);
        $this->createIndex('idx_description_pack_id_6803_01', 'description', 'description_pack_id', 0);
        $this->createIndex('idx_UNIQUE_username_7323_02', 'user', 'username', 1);
        $this->createIndex('idx_UNIQUE_email_7323_03', 'user', 'email', 1);
        $this->createIndex('idx_UNIQUE_password_reset_token_7323_04', 'user', 'password_reset_token', 1);

        $this->execute('SET foreign_key_checks = 0');
        $this->addForeignKey('fk_person_6723_00', '{{%character}}', 'person_id', '{{%person}}', 'person_id', 'CASCADE',
            'NO ACTION');
        $this->addForeignKey('fk_description_pack_6803_01', '{{%description}}', 'description_pack_id',
            '{{%description_pack}}', 'description_pack_id', 'CASCADE', 'NO ACTION');
        $this->execute('SET foreign_key_checks = 1;');
    }

    public function safeDown()
    {
        $this->execute('SET foreign_key_checks = 0');
        $this->execute('DROP TABLE IF EXISTS `character`');
        $this->execute('SET foreign_key_checks = 1;');
        $this->execute('SET foreign_key_checks = 0');
        $this->execute('DROP TABLE IF EXISTS `description`');
        $this->execute('SET foreign_key_checks = 1;');
        $this->execute('SET foreign_key_checks = 0');
        $this->execute('DROP TABLE IF EXISTS `description_pack`');
        $this->execute('SET foreign_key_checks = 1;');
        $this->execute('SET foreign_key_checks = 0');
        $this->execute('DROP TABLE IF EXISTS `group`');
        $this->execute('SET foreign_key_checks = 1;');
        $this->execute('SET foreign_key_checks = 0');
        $this->execute('DROP TABLE IF EXISTS `person`');
        $this->execute('SET foreign_key_checks = 1;');
        $this->execute('SET foreign_key_checks = 0');
        $this->execute('DROP TABLE IF EXISTS `recap`');
        $this->execute('SET foreign_key_checks = 1;');
        $this->execute('SET foreign_key_checks = 0');
        $this->execute('DROP TABLE IF EXISTS `story`');
        $this->execute('SET foreign_key_checks = 1;');
        $this->execute('SET foreign_key_checks = 0');
        $this->execute('DROP TABLE IF EXISTS `user`');
        $this->execute('SET foreign_key_checks = 1;');
    }
}
