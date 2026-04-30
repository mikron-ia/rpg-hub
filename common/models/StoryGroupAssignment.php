<?php

namespace common\models;

use common\models\core\HasKey;
use common\models\core\HasVisibility;
use common\models\core\IsAssignment;
use common\models\core\Visibility;
use common\models\tools\ToolsForEntity;
use common\models\tools\ToolsForHasVisibility;
use Override;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Exception;
use yii\web\HttpException;

/**
 * @property int $story_group_assignment_id
 * @property int $group_id
 * @property int $story_id
 * @property string $key
 * @property string $visibility
 * @property int|null $position
 * @property string|null $short_text
 * @property string|null $public_text
 * @property string|null $private_text
 *
 * @property Group $group
 * @property Story $story
 */
class StoryGroupAssignment extends ActiveRecord implements HasKey, HasVisibility, IsAssignment
{
    use ToolsForEntity;
    use ToolsForHasVisibility;

    #[Override]
    public static function tableName(): string
    {
        return 'story_group_assignment';
    }

    #[Override]
    public static function keyParameterName(): string
    {
        return 'storyGroupAssignment';
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
                'targetAttribute' => ['group_id' => 'group_id'],
            ],
            [
                ['story_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Story::class,
                'targetAttribute' => ['story_id' => 'story_id'],
            ],
        ];
    }

    #[Override]
    public function attributeLabels(): array
    {
        return [
            'story_group_assignment_id' => Yii::t('app', 'STORY_GROUP_ASSIGNMENT_ID'),
            'group_id' => Yii::t('app', 'STORY_GROUP_ASSIGNMENT_GROUP_ID'),
            'story_id' => Yii::t('app', 'STORY_GROUP_ASSIGNMENT_STORY_ID'),
            'key' => Yii::t('app', 'STORY_GROUP_ASSIGNMENT_KEY'),
            'visibility' => Yii::t('app', 'LABEL_VISIBILITY'),
        ];
    }

    /**
     * @throws HttpException
     */
    #[Override]
    public function beforeSave($insert): bool
    {
        if ($insert) {
            $this->key = $this->generateKey();
        }

        return parent::beforeSave($insert);
    }

    public function getGroup(): ActiveQuery
    {
        return $this->hasOne(Group::class, ['group_id' => 'group_id']);
    }

    public function getStory(): ActiveQuery
    {
        return $this->hasOne(Story::class, ['story_id' => 'story_id']);
    }

    /**
     * @throws Exception
     */
    public static function create(int $groupId, int $storyId, Visibility $visibility): self
    {
        $assignment = new self();

        $assignment->group_id = $groupId;
        $assignment->story_id = $storyId;
        $assignment->visibility = $visibility->value;

        // the value is discarded because this it returns false only on validation error and all data is internal
        $assignment->save();

        return $assignment;
    }
}
