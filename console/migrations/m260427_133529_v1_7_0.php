<?php

use common\models\core\ImageDisplayMode;
use common\models\core\ImportanceCategory;
use common\models\core\Visibility;
use common\models\type\AssignmentRank;
use common\models\type\StoryType;
use yii\db\Migration;

class m260427_133529_v1_7_0 extends Migration
{
    #[Override]
    public function safeUp(): void
    {
        $this->createTable('{{%location}}', [
            'location_id' => $this->primaryKey()->unsigned(),
            'epic_id' => $this->integer(11)->unsigned()->notNull(),
            'key' => $this->string(80)->notNull(),
            'name' => $this->string(120)->notNull(),
            'tagline' => $this->string(120)->notNull(),
            'visibility' => $this->string(20)->notNull()->defaultValue(Visibility::VISIBILITY_GM->value),
            'importance_category' => $this->string(20)->notNull()->defaultValue(ImportanceCategory::IMPORTANCE_MEDIUM->value),
            'updated_at' => $this->integer(11)->unsigned(),
            'modified_at' => $this->integer(11)->unsigned(),
            'description_pack_id' => $this->integer(11)->unsigned(),
            'importance_pack_id' => $this->integer(11)->unsigned(),
            'scribble_pack_id' => $this->integer(11)->unsigned(),
            'seen_pack_id' => $this->integer(11)->unsigned(),
            'utility_bag_id' => $this->integer(11)->unsigned(),
        ]);

        $this->createTable('{{%image}}', [
            'image_id' => $this->primaryKey()->unsigned(),
            'epic_id' => $this->integer(11)->unsigned()->notNull(),
            'key' => $this->string(80)->notNull(),
            'name' => $this->string(120)->null(),
            'note' => $this->text()->null(),
            'title' => $this->string(120)->null(),
            'alt' => $this->string(255)->null(),
            'display_height' => $this->smallInteger()->unsigned()->null(),
            'display_width' => $this->smallInteger()->unsigned()->null(),
            'created_at' => $this->integer(11)->unsigned()->notNull(),
            'updated_at' => $this->integer(11)->unsigned()->notNull(),
            'created_by' => $this->integer()->unsigned()->notNull(),
            'updated_by' => $this->integer()->unsigned()->notNull(),
        ]);

        $this->addForeignKey(
            'image_user_creator',
            'image',
            'created_by',
            'user',
            'id',
            'RESTRICT',
            'CASCADE'
        );
        $this->addForeignKey(
            'image_user_modifier',
            'image',
            'updated_by',
            'user',
            'id',
            'RESTRICT',
            'CASCADE'
        );

        $this->createTable('{{%image_link}}', [
            'image_link_id' => $this->primaryKey()->unsigned(),
            'image_id' => $this->integer(11)->unsigned()->notNull(),
            'key' => $this->string(80)->notNull(),
            'link' => $this->string(255)->notNull(),
            'display_mode' => $this->char(6)->notNull()->defaultValue(ImageDisplayMode::Always->value),
            'display_weight' => $this->smallInteger()->unsigned()->notNull()->defaultValue(100),
            'created_at' => $this->integer(11)->unsigned()->notNull(),
            'updated_at' => $this->integer(11)->unsigned()->notNull(),
            'created_by' => $this->integer()->unsigned()->notNull(),
            'updated_by' => $this->integer()->unsigned()->notNull(),
        ]);

        $this->addForeignKey(
            'image_link_image',
            'image_link',
            'image_id',
            'image',
            'image_id',
            'RESTRICT',
            'CASCADE'
        );

        $this->addForeignKey(
            'image_link_user_creator',
            'image_link',
            'created_by',
            'user',
            'id',
            'RESTRICT',
            'CASCADE'
        );
        $this->addForeignKey(
            'image_link_user_modifier',
            'image_link',
            'updated_by',
            'user',
            'id',
            'RESTRICT',
            'CASCADE'
        );

        $assignmentRankColumn = $this->char(5)->notNull()->defaultValue(AssignmentRank::Other->value)->after('key');
        $this->addColumn('{{%story_character_assignment}}', 'rank', $assignmentRankColumn);
        $this->addColumn('{{%story_group_assignment}}', 'rank', $assignmentRankColumn);

        $this->alterColumn('{{%story}}', 'code', $this->char(10)->notNull()->defaultValue(StoryType::None->value));
        $this->execute(sprintf("UPDATE `story` SET `code` = '%s' WHERE `code` = ''", StoryType::None->value));

        $this->addColumn('{{%seen}}', 'times', $this->bigInteger()->unsigned()->defaultValue(0));
    }

    #[Override]
    public function safeDown(): void
    {
        $this->dropColumn('{{%seen}}', 'times');

        $this->execute(sprintf("UPDATE `story` SET `code` = '' WHERE `code` = '%s'", StoryType::None->value));
        $this->alterColumn('{{%story}}', 'code', $this->string(40)->notNull());

        $this->dropColumn('{{%story_group_assignment}}', 'rank');
        $this->dropColumn('{{%story_character_assignment}}', 'rank');

        $this->dropForeignKey('image_link_user_modifier', 'image_link');
        $this->dropForeignKey('image_link_user_creator', 'image_link');

        $this->dropForeignKey('image_link_image', 'image_link');

        $this->dropTable('{{%image_link}}');

        $this->dropForeignKey('image_user_modifier', 'image');
        $this->dropForeignKey('image_user_creator', 'image');

        $this->dropTable('{{%image}}');

        $this->dropTable('{{%location}}');
    }
}
