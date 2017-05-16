<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "article".
 *
 * @property string $article_id
 * @property string $epic_id
 * @property string $key
 * @property string $title
 * @property string $subtitle
 * @property string $visibility
 * @property string $text_raw
 * @property string $text_ready
 */
class Article extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'article';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['epic_id'], 'integer'],
            [['key', 'title', 'text_raw', 'text_ready'], 'required'],
            [['text_raw', 'text_ready'], 'string'],
            [['key'], 'string', 'max' => 80],
            [['title', 'subtitle'], 'string', 'max' => 120],
            [['visibility'], 'string', 'max' => 20],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'article_id' => Yii::t('app', 'Article ID'),
            'epic_id' => Yii::t('app', 'Epic ID'),
            'key' => Yii::t('app', 'Key'),
            'title' => Yii::t('app', 'Title'),
            'subtitle' => Yii::t('app', 'Subtitle'),
            'visibility' => Yii::t('app', 'Visibility'),
            'text_raw' => Yii::t('app', 'Text Raw'),
            'text_ready' => Yii::t('app', 'Text Ready'),
        ];
    }
}
