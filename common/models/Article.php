<?php

namespace common\models;

use common\models\core\HasVisibility;
use common\models\core\Visibility;
use Yii;
use yii\db\ActiveRecord;

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
 *
 * @property Epic $epic
 */
class Article extends ActiveRecord implements HasVisibility
{
    public static function tableName()
    {
        return 'article';
    }

    public function rules()
    {
        return [
            [['epic_id'], 'integer'],
            [['title', 'text_raw'], 'required'],
            [['text_raw'], 'string'],
            [['title', 'subtitle'], 'string', 'max' => 120],
            [['visibility'], 'string', 'max' => 20],
            [['epic_id'], 'exist', 'skipOnError' => true, 'targetClass' => Epic::className(), 'targetAttribute' => ['epic_id' => 'epic_id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'article_id' => Yii::t('app', 'ARTICLE_ID'),
            'epic_id' => Yii::t('app', 'ARTICLE_EPIC_ID'),
            'key' => Yii::t('app', 'ARTICLE_KEY'),
            'title' => Yii::t('app', 'ARTICLE_TITLE'),
            'subtitle' => Yii::t('app', 'ARTICLE_SUBTITLE'),
            'visibility' => Yii::t('app', 'ARTICLE_VISIBILITY'),
            'text_raw' => Yii::t('app', 'ARTICLE_TEXT'),
            'text_ready' => Yii::t('app', 'ARTICLE_TEXT'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEpic()
    {
        return $this->hasOne(Epic::className(), ['epic_id' => 'epic_id']);
    }

    static public function allowedVisibilities():array
    {
        return [
            Visibility::VISIBILITY_GM,
            Visibility::VISIBILITY_FULL
        ];
    }

    public function getVisibility():string
    {
        $visibility = Visibility::create($this->visibility);
        return $visibility->getName();
    }

    public function getVisibilityLowercase():string
    {
        $visibility = Visibility::create($this->visibility);
        return $visibility->getNameLowercase();
    }
}
