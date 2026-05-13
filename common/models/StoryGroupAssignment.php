<?php

namespace common\models;

use common\models\core\HasKey;
use common\models\core\HasVisibility;
use common\models\core\IsAssignment;
use common\models\core\ToolsForIsAssignment;
use common\models\core\Visibility;
use common\models\tools\ToolsForEntity;
use common\models\tools\ToolsForHasVisibility;
use common\models\type\AssignmentRank;
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
 * @property string $rank
 *
 * @property Group $group
 * @property Story $story
 */
class StoryGroupAssignment extends ActiveRecord implements HasKey, HasVisibility, IsAssignment
{
    use ToolsForEntity;
    use ToolsForHasVisibility;
    use ToolsForIsAssignment;

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
            'rank' => Yii::t('app', 'STORY_GROUP_ASSIGNMENT_RANK'),
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
    #[Override]
    public static function create(
        int $actingSideId,
        int $narrativeSideId,
        Visibility $visibility,
        AssignmentRank $rank
    ): self {
        $assignment = new self();

        $assignment->group_id = $actingSideId;
        $assignment->story_id = $narrativeSideId;
        $assignment->visibility = $visibility->value;
        $assignment->rank = $rank->value;

        // the value is discarded because this it returns false only on validation error and all data is internal
        $assignment->save();

        return $assignment;
    }

    #[Override]
    public function getActingSideId(): int
    {
        return $this->group_id;
    }

    #[Override]
    public function getNarrativeSideId(): int
    {
        return $this->story_id;
    }

    #[Override]
    public function getRank(): AssignmentRank
    {
        return AssignmentRank::from($this->rank);
    }
}
