<?php

namespace common\models;

use Yii;
use yii\helpers\Markdown;

/**
 * This is the model class for table "story".
 *
 * @property string $story_id
 * @property string $epic_id
 * @property string $key
 * @property string $name
 * @property string $short
 * @property string $long
 * @property string $data
 *
 * @property Epic $epic
 * @property StoryParameter[] $storyParameters
 */
class Story extends \yii\db\ActiveRecord implements Displayable
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'story';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['epic_id', 'key', 'name', 'short', 'data'], 'required'],
            [['epic_id'], 'integer'],
            [['short', 'long', 'data'], 'string'],
            [['key'], 'string', 'max' => 80],
            [['name'], 'string', 'max' => 120],
            [
                ['epic_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Epic::className(),
                'targetAttribute' => ['epic_id' => 'epic_id']
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'story_id' => Yii::t('app', 'STORY_ID'),
            'epic_id' => Yii::t('app', 'LABEL_EPIC'),
            'key' => Yii::t('app', 'STORY_KEY'),
            'name' => Yii::t('app', 'STORY_NAME'),
            'short' => Yii::t('app', 'STORY_SHORT'),
            'long' => Yii::t('app', 'STORY_LONG'),
            'data' => Yii::t('app', 'STORY_DATA'),
            'storyParameters' => Yii::t('app', 'STORY_PARAMETERS'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEpic()
    {
        return $this->hasOne(Epic::className(), ['epic_id' => 'epic_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStoryParameters()
    {
        return $this->hasMany(StoryParameter::className(), ['story_id' => 'story_id']);
    }


    /**
     * Provides story summary formatted in HTML
     * @return string Short summary formatted to HTML
     */
    public function getShortFormatted()
    {
        return Markdown::process($this->short, 'gfm');
    }

    /**
     * Provides story summary formatted in HTML
     * @return string Long summary formatted to HTML
     */
    public function getLongFormatted()
    {
        return Markdown::process($this->long, 'gfm');
    }

    public function formatParameters()
    {
        $parameters = [];

        foreach ($this->storyParameters as $storyParameter) {
            if ($storyParameter->visibility == StoryParameter::VISIBILITY_FULL) {
                $parameters[] = [
                    'name' => $storyParameter->getCodeName(),
                    'value' => $storyParameter->content,
                ];
            }
        }

        return $parameters;
    }

    /**
     * @inheritdoc
     */
    public function getSimpleData()
    {
        return [
            'name' => $this->name,
            'key' => $this->key,
            'parameters' => $this->formatParameters(),
            'short' => $this->getShortFormatted(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function getCompleteData()
    {

        $basicData = [
            'name' => $this->name,
            'key' => $this->key,
            'help' => [],
            'parameters' => $this->formatParameters(),
            'short' => $this->getShortFormatted(),
            'long' => $this->getLongFormatted(),
        ];
        return $basicData;
    }
}
