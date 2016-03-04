<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "story".
 *
 * @property string $story_id
 * @property string $key
 * @property string $name
 * @property string $data
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
            [['key', 'name', 'data'], 'required'],
            [['data'], 'string'],
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
            'story_id' => Yii::t('app', 'Story ID'),
            'key' => Yii::t('app', 'Story Key'),
            'name' => Yii::t('app', 'Title'),
            'data' => Yii::t('app', 'Data'),
        ];
    }

    /**
     * @inheritdoc
     * @return StoryQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new StoryQuery(get_called_class());
    }
}
