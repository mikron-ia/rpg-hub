<?php

namespace common\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "story_character_assignment".
 *
 * @property int $story_character_assignment_id
 * @property int $character_id
 * @property int $story_id
 * @property string $visibility
 * @property int|null $position
 * @property string|null $short_text
 * @property string|null $public_text
 * @property string|null $private_text
 *
 * @property Character $character
 * @property Story $story
 */
class StoryCharacterAssignment extends ActiveRecord
{
    public static function tableName(): string
    {
        return 'story_character_assignment';
    }

    public function rules(): array
    {
        return [
            [['character_id', 'story_id'], 'required'],
            [['character_id', 'story_id', 'position'], 'integer'],
            [['public_text', 'private_text'], 'string'],
            [['visibility'], 'string', 'max' => 20],
            [['short_text'], 'string', 'max' => 80],
            [
                ['character_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Character::class,
                'targetAttribute' => ['character_id' => 'character_id']
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
            'story_character_assignment_id' => Yii::t('app', 'STORY_CHARACTER_ASSIGNMENT_ID'),
            'character_id' => Yii::t('app', 'STORY_CHARACTER_ASSIGNMENT_CHARACTER_ID'),
            'story_id' => Yii::t('app', 'STORY_CHARACTER_ASSIGNMENT_STORY_ID'),
            'visibility' => Yii::t('app', 'LABEL_VISIBILITY'),
        ];
    }

    public function getCharacter(): ActiveQuery
    {
        return $this->hasOne(Character::class, ['character_id' => 'character_id']);
    }

    public function getStory(): ActiveQuery
    {
        return $this->hasOne(Story::class, ['story_id' => 'story_id']);
    }
}
