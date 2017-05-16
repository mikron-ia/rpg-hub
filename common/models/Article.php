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
            [['key', 'title', 'text_raw', 'text_ready'], 'required'],
            [['text_raw', 'text_ready'], 'string'],
            [['key'], 'string', 'max' => 80],
            [['title', 'subtitle'], 'string', 'max' => 120],
            [['visibility'], 'string', 'max' => 20],
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
