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
            [['epic_id'], 'exist', 'skipOnError' => true, 'targetClass' => Epic::className(), 'targetAttribute' => ['epic_id' => 'epic_id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'story_id' => Yii::t('app', 'STORY_ID'),
            'key' => Yii::t('app', 'STORY_KEY'),
            'name' => Yii::t('app', 'STORY_NAME'),
            'short' => Yii::t('app', 'STORY_SHORT'),
            'long' => Yii::t('app', 'STORY_LONG'),
            'data' => Yii::t('app', 'STORY_DATA'),
            'storyParameters' => Yii::t('app', 'STORY_PARAMETERS'),
        ];
    }

    /**
     * @return string Short summary formatted to HTML
     */
    public function getShortFormatted()
    {
        return Markdown::process($this->short, 'gfm');
    }

    /**
     * @return string Long summary formatted to HTML
     */
    public function getLongFormatted()
    {
        return Markdown::process($this->long, 'gfm');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStoryParameters()
    {
        return $this->hasMany(StoryParameter::className(), ['story_id' => 'story_id']);
    }

    /**
     * @return array Simple representation of the object content, fit for basic display
     */
    public function getSimpleData()
    {
        return [
            'name' => $this->name,
            'key' => $this->key,
        ];
    }

    /**
     * @return array Complete representation of public parts of object content, fit for full card display
     */
    public function getCompleteData()
    {
        $parameters = [];

        foreach ($this->storyParameters as $storyParameter) {
            $parameters[] = [
                'name' => $storyParameter->getCodeName(),
                'value' => $storyParameter->content,
            ];
        }

        $basicData = [
            'name' => $this->name,
            'key' => $this->key,
            'help' => [],
            'parameters' => $parameters,
            'short' => $this->getShortFormatted(),
            'long' => $this->getLongFormatted(),
        ];
        return $basicData;
    }
}
