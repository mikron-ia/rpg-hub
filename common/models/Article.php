<?php

namespace common\models;

use common\models\core\HasEpicControl;
use common\models\core\HasVisibility;
use common\models\core\Visibility;
use common\models\tools\ToolsForEntity;
use Yii;
use yii\db\ActiveRecord;
use yii\helpers\Html;
use yii\helpers\Markdown;
use yii2tech\ar\position\PositionBehavior;

/**
 * This is the model class for table "article".
 *
 * @property string $article_id
 * @property string $epic_id
 * @property string $key
 * @property string $title
 * @property string $subtitle
 * @property string $visibility
 * @property string $seen_pack_id
 * @property string $description_pack_id
 * @property integer $position
 * @property string $text_raw
 * @property string $text_ready
 *
 * @property DescriptionPack $descriptionPack
 * @property Epic $epic
 * @property SeenPack $seenPack
 */
class Article extends ActiveRecord implements HasEpicControl, HasVisibility
{
    use ToolsForEntity;

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
            [['description_pack_id'], 'exist', 'skipOnError' => true, 'targetClass' => DescriptionPack::className(), 'targetAttribute' => ['description_pack_id' => 'description_pack_id']],
            [['epic_id'], 'exist', 'skipOnError' => true, 'targetClass' => Epic::className(), 'targetAttribute' => ['epic_id' => 'epic_id']],
            [['seen_pack_id'], 'exist', 'skipOnError' => true, 'targetClass' => SeenPack::className(), 'targetAttribute' => ['seen_pack_id' => 'seen_pack_id']],
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
            'position' => Yii::t('app', 'ARTICLE_POSITION'),
            'text_raw' => Yii::t('app', 'ARTICLE_TEXT'),
            'text_ready' => Yii::t('app', 'ARTICLE_TEXT'),
        ];
    }

    public function behaviors()
    {
        return [
            'positionBehavior' => [
                'class' => PositionBehavior::className(),
                'positionAttribute' => 'position',
                'groupAttributes' => ['epic_id'],
            ],
        ];
    }

    public function beforeSave($insert)
    {
        if ($insert) {
            $this->key = $this->generateKey('article');
        }

        if (empty($this->seen_pack_id)) {
            $pack = SeenPack::create('Article');
            $this->seen_pack_id = $pack->seen_pack_id;
        }

        if (empty($this->description_pack_id)) {
            $pack = DescriptionPack::create('Article');
            $this->description_pack_id = $pack->description_pack_id;
        }

        /**
         * @todo: Improve parsing routine that works the text over
         */
        $this->text_ready =  Markdown::process(Html::encode($this->text_raw), 'gfm');

        return parent::beforeSave($insert);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDescriptionPack()
    {
        return $this->hasOne(DescriptionPack::className(), ['description_pack_id' => 'description_pack_id']);
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

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSeenPack()
    {
        return $this->hasOne(SeenPack::className(), ['seen_pack_id' => 'seen_pack_id']);
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

    static public function canUserIndexThem():bool
    {
        return self::canUserIndexInEpic(Yii::$app->params['activeEpic']);
    }

    static public function canUserCreateThem():bool
    {
        return self::canUserCreateInEpic(Yii::$app->params['activeEpic']);
    }

    public function canUserControlYou():bool
    {
        return self::canUserControlInEpic($this->epic);
    }

    public function canUserViewYou():bool
    {
        return self::canUserViewInEpic($this->epic);
    }

    static function throwExceptionAboutCreate()
    {
        self::thrownExceptionAbout(Yii::t('app', 'NO_RIGHTS_TO_CREATE_ARTICLE'));
    }

    static function throwExceptionAboutControl()
    {
        self::thrownExceptionAbout(Yii::t('app', 'NO_RIGHT_TO_CONTROL_ARTICLE'));
    }

    static function throwExceptionAboutIndex()
    {
        self::thrownExceptionAbout(Yii::t('app', 'NO_RIGHTS_TO_LIST_ARTICLE'));
    }

    static function throwExceptionAboutView()
    {
        self::thrownExceptionAbout(Yii::t('app', 'NO_RIGHT_TO_VIEW_ARTICLE'));
    }
}
