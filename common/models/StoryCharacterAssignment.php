<?php

namespace common\models;

use common\models\core\HasKey;
use common\models\core\IsAssignment;
use common\models\core\Visibility;
use common\models\tools\ToolsForEntity;
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
 * @property int|null $position
 * @property string|null $short_text
 * @property string|null $public_text
 * @property string|null $private_text
 *
 * @property Character $character
 * @property Story $story
 */
class StoryCharacterAssignment extends ActiveRecord implements HasKey, IsAssignment
{
    use ToolsForEntity;

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
    public static function create(int $characterId, int $storyId, Visibility $visibility): self
    {
        $assignment = new self();

        $assignment->character_id = $characterId;
        $assignment->story_id = $storyId;
        $assignment->visibility = $visibility->value;

        // the value is discarded because this it returns false only on validation error and all data is internal
        $assignment->save();

        return $assignment;
    }
}
