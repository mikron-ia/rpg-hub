<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "story_parameter".
 *
 * @property string $story_parameter_id
 * @property string $story_id
 * @property string $code
 * @property string $visibility
 * @property string $content
 *
 * @property Story $story
 */
class StoryParameter extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'story_parameter';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['story_id', 'code', 'name', 'content'], 'required'],
            [['story_id'], 'integer'],
            [['visibility'], 'string'],
            [['code'], 'string', 'max' => 20],
            [['content'], 'string', 'max' => 80],
            [['story_id'], 'exist', 'skipOnError' => true, 'targetClass' => Story::className(), 'targetAttribute' => ['story_id' => 'story_id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'story_parameter_id' => Yii::t('app', 'STORY_PARAMETER_ID'),
            'story_id' => Yii::t('app', 'STORY_ID'),
            'code' => Yii::t('app', 'STORY_PARAMETER_CODE'),
            'visibility' => Yii::t('app', 'STORY_PARAMETER_VISIBILITY'),
            'content' => Yii::t('app', 'STORY_PARAMETER_CONTENT'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStory()
    {
        return $this->hasOne(Story::className(), ['story_id' => 'story_id']);
    }

    /**
     * @inheritdoc
     * @return StoryParameterQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new StoryParameterQuery(get_called_class());
    }
}
