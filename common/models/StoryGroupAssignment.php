<?php

namespace common\models;

use common\models\core\HasVisibility;
use common\models\tools\ToolsForHasVisibility;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;


/**
 * This is the model class for table "story_group_assignment".
 *
 * @property int $story_group_assignment_id
 * @property int $group_id
 * @property int $story_id
 * @property string $visibility
 * @property int|null $position
 * @property string|null $short_text
 * @property string|null $public_text
 * @property string|null $private_text
 *
 * @property Group $group
 * @property Story $story
 */
class StoryGroupAssignment extends ActiveRecord implements HasVisibility
{
    use ToolsForHasVisibility;

    public static function tableName(): string
    {
        return 'story_group_assignment';
    }

    public function rules(): array
    {
        return [
            [['group_id', 'story_id'], 'required'],
            [['group_id', 'story_id'], 'integer'],
            [['visibility'], 'string', 'max' => 20],
            [
                ['group_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Group::class,
                'targetAttribute' => ['group_id' => 'group_id']
            ],
            [
                ['story_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Story::class,
                'targetAttribute' => ['story_id' => 'story_id']
            ],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'story_group_assignment_id' => Yii::t('app', 'STORY_GROUP_ASSIGNMENT_ID'),
            'group_id' => Yii::t('app', 'STORY_GROUP_ASSIGNMENT_GROUP_ID'),
            'story_id' => Yii::t('app', 'STORY_GROUP_ASSIGNMENT_STORY_ID'),
            'visibility' => Yii::t('app', 'LABEL_VISIBILITY'),
        ];
    }

    public function getGroup(): ActiveQuery
    {
        return $this->hasOne(Group::class, ['group_id' => 'group_id']);
    }

    public function getStory(): ActiveQuery
    {
        return $this->hasOne(Story::class, ['story_id' => 'story_id']);
    }
}
