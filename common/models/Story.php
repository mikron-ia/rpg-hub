<?php

namespace common\models;

use Yii;
use yii\helpers\Markdown;

/**
 * This is the model class for table "story".
 *
 * @property string $story_id
 * @property string $key
 * @property string $name
 * @property string $short
 * @property string $long
 * @property string $data
 *
 * @property StoryParameter[] $storyParameters
 */
class Story extends \yii\db\ActiveRecord
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
            [['key', 'name', 'short', 'data'], 'required'],
            [['short', 'long', 'data'], 'string'],
            [['key'], 'string', 'max' => 80],
            [['name'], 'string', 'max' => 120]
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
}
