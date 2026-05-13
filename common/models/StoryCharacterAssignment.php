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
 * @property int $story_character_assignment_id
 * @property int $character_id
 * @property int $story_id
 * @property string $key
 * @property string $visibility
 * @property string $rank
 *
 * @property Character $character
 * @property Story $story
 */
class StoryCharacterAssignment extends ActiveRecord implements HasKey, HasVisibility, IsAssignment
{
    use ToolsForEntity;
    use ToolsForHasVisibility;
    use ToolsForIsAssignment;

    #[Override]
    public static function tableName(): string
    {
        return 'story_character_assignment';
    }

    #[Override]
    public static function keyParameterName(): string
    {
        return 'storyCharacterAssignment';
    }

    #[Override]
    public function rules(): array
    {
        return [
            [['character_id', 'story_id'], 'required'],
            [['character_id', 'story_id'], 'integer'],
            [['visibility'], 'string', 'max' => 20],
            [
                ['character_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Character::class,
                'targetAttribute' => ['character_id' => 'character_id'],
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
            'story_character_assignment_id' => Yii::t('app', 'STORY_CHARACTER_ASSIGNMENT_ID'),
            'character_id' => Yii::t('app', 'STORY_CHARACTER_ASSIGNMENT_CHARACTER_ID'),
            'story_id' => Yii::t('app', 'STORY_CHARACTER_ASSIGNMENT_STORY_ID'),
            'key' => Yii::t('app', 'STORY_CHARACTER_ASSIGNMENT_KEY'),
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

    public function getCharacter(): ActiveQuery
    {
        return $this->hasOne(Character::class, ['character_id' => 'character_id']);
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

        $assignment->character_id = $actingSideId;
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
        return $this->character_id;
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
