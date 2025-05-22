<?php

namespace common\models;

use common\models\core\HasEpicControl;
use common\models\core\HasSightings;
use common\models\core\HasVisibility;
use common\models\core\Visibility;
use common\models\tools\ToolsForHasVisibility;
use common\models\tools\ToolsForLinkTags;
use common\models\tools\ToolsForEntity;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\Html;
use yii\helpers\Markdown;
use yii\helpers\StringHelper;
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
 * @property string $outline_raw
 * @property string $outline_ready
 * @property string $text_raw
 * @property string $text_ready
 * @property string $utility_bag_id
 *
 * @property DescriptionPack $descriptionPack
 * @property Epic $epic
 * @property SeenPack $seenPack
 * @property UtilityBag $utilityBag
 */
class Article extends ActiveRecord implements HasEpicControl, HasVisibility, HasSightings
{
    use ToolsForEntity;
    use ToolsForLinkTags;
    use ToolsForHasVisibility;

    public bool $is_off_the_record_change = false;

    public static function tableName(): string
    {
        return 'article';
    }

    public function rules(): array
    {
        return [
            [['epic_id'], 'integer'],
            [['title', 'text_raw'], 'required'],
            [['text_raw', 'outline_raw'], 'string'],
            [['title', 'subtitle'], 'string', 'max' => 120],
            [['visibility'], 'string', 'max' => 20],
            [['is_off_the_record_change'], 'boolean'],
            [
                ['description_pack_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => DescriptionPack::class,
                'targetAttribute' => ['description_pack_id' => 'description_pack_id']
            ],
            [
                ['epic_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Epic::class,
                'targetAttribute' => ['epic_id' => 'epic_id']
            ],
            [
                ['seen_pack_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => SeenPack::class,
                'targetAttribute' => ['seen_pack_id' => 'seen_pack_id']
            ],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'article_id' => Yii::t('app', 'ARTICLE_ID'),
            'epic_id' => Yii::t('app', 'ARTICLE_EPIC_ID'),
            'key' => Yii::t('app', 'ARTICLE_KEY'),
            'title' => Yii::t('app', 'ARTICLE_TITLE'),
            'subtitle' => Yii::t('app', 'ARTICLE_SUBTITLE'),
            'visibility' => Yii::t('app', 'ARTICLE_VISIBILITY'),
            'position' => Yii::t('app', 'ARTICLE_POSITION'),
            'outline_raw' => Yii::t('app', 'ARTICLE_OUTLINE'),
            'outline_ready' => Yii::t('app', 'ARTICLE_OUTLINE'),
            'text_raw' => Yii::t('app', 'ARTICLE_TEXT'),
            'text_ready' => Yii::t('app', 'ARTICLE_TEXT'),
            'utility_bag_id' => Yii::t('app', 'UTILITY_BAG'),
            'is_off_the_record_change' => Yii::t('app', 'CHECK_OFF_THE_RECORD_CHANGE'),
        ];
    }

    public function afterFind(): void
    {
        if ($this->seen_pack_id) {
            $this->seenPack->recordNotification();
        }
        parent::afterFind();
    }

    public function behaviors(): array
    {
        return [
            'positionBehavior' => [
                'class' => PositionBehavior::class,
                'positionAttribute' => 'position',
                'groupAttributes' => ['epic_id'],
            ],
        ];
    }

    public function beforeSave($insert): bool
    {
        if ($insert) {
            $this->key = $this->generateKey('article');
        }

        if (empty($this->seen_pack_id)) {
            $pack = SeenPack::create('Article');
            $this->seen_pack_id = $pack->seen_pack_id;
        }

        if (empty($this->utility_bag_id)) {
            $pack = UtilityBag::create('Article');
            $this->utility_bag_id = $pack->utility_bag_id;
        }

        if (empty($this->description_pack_id)) {
            $pack = DescriptionPack::create('Article');
            $this->description_pack_id = $pack->description_pack_id;
        }

        $this->text_ready = Markdown::process(
            Html::encode($this->processAllInOrder($this->text_raw)),
            'gfm'
        );
        $this->outline_ready = Markdown::process(
            Html::encode($this->processAllInOrder($this->outline_raw ?? '')),
            'gfm'
        );

        return parent::beforeSave($insert);
    }

    public function afterSave($insert, $changedAttributes): void
    {
        if (!$this->is_off_the_record_change) {
            $this->seenPack->updateRecord();
        }
        parent::afterSave($insert, $changedAttributes);
    }

    /**
     * @return ActiveQuery
     */
    public function getDescriptionPack(): ActiveQuery
    {
        return $this->hasOne(DescriptionPack::class, ['description_pack_id' => 'description_pack_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getEpic(): ActiveQuery
    {
        return $this->hasOne(Epic::class, ['epic_id' => 'epic_id']);
    }

    public function getOutlineWordCount(): int
    {
        return StringHelper::countWords($this->outline_raw ?? '');
    }

    public function getTextWordCount(): int
    {
        return StringHelper::countWords($this->text_raw ?? '');
    }

    public function recordSighting(): bool
    {
        return $this->seenPack->recordSighting();
    }

    public function recordNotification(): bool
    {
        return $this->seenPack->recordNotification();
    }

    public function showSightingStatus(): string
    {
        return $this->seenPack->getStatusForCurrentUser();
    }

    public function showSightingCSS(): string
    {
        return $this->seenPack->getCSSForCurrentUser();
    }

    public function getSeenPack(): ActiveQuery
    {
        return $this->hasOne(SeenPack::class, ['seen_pack_id' => 'seen_pack_id']);
    }

    static public function canUserIndexThem(): bool
    {
        return self::canUserIndexInEpic(Yii::$app->params['activeEpic']);
    }

    static public function canUserCreateThem(): bool
    {
        return self::canUserCreateInEpic(Yii::$app->params['activeEpic']);
    }

    public function canUserControlYou(): bool
    {
        return self::canUserControlInEpic($this->epic);
    }

    public function canUserViewYou(): bool
    {
        return self::canUserViewInEpic($this->epic);
    }

    static public function throwExceptionAboutCreate(): void
    {
        self::thrownExceptionAbout(Yii::t('app', 'NO_RIGHTS_TO_CREATE_ARTICLE'));
    }

    static public function throwExceptionAboutControl(): void
    {
        self::thrownExceptionAbout(Yii::t('app', 'NO_RIGHT_TO_CONTROL_ARTICLE'));
    }

    static public function throwExceptionAboutIndex(): void
    {
        self::thrownExceptionAbout(Yii::t('app', 'NO_RIGHTS_TO_LIST_ARTICLE'));
    }

    static public function throwExceptionAboutView(): void
    {
        self::thrownExceptionAbout(Yii::t('app', 'NO_RIGHT_TO_VIEW_ARTICLE'));
    }
}
