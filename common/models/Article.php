<?php

namespace common\models;

use common\models\core\HasEpicControl;
use common\models\core\HasKey;
use common\models\core\HasSightings;
use common\models\core\HasVisibility;
use common\models\core\IsLinkable;
use common\models\core\Visibility;
use common\models\tools\ToolsForHasVisibility;
use common\models\tools\ToolsForLinkTags;
use common\models\tools\ToolsForEntity;
use Override;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Exception;
use yii\helpers\Html;
use yii\helpers\Markdown;
use yii\helpers\StringHelper;
use yii\web\HttpException;
use yii2tech\ar\position\PositionBehavior;

/**
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
 * @property int $bestowed_list_id
 *
 * @property BestowedList $bestowedList
 * @property DescriptionPack $descriptionPack
 * @property Epic $epic
 * @property SeenPack $seenPack
 * @property UtilityBag $utilityBag
 *
 * @method movePrev()
 * @method moveNext()
 */
class Article extends ActiveRecord implements HasEpicControl, HasVisibility, HasSightings, HasKey, IsLinkable
{
    use ToolsForEntity;
    use ToolsForLinkTags;
    use ToolsForHasVisibility;

    public bool $is_off_the_record_change = false;

    public array|string $bestowedAccessIds = [];

    #[Override]
    public static function tableName(): string
    {
        return 'article';
    }

    #[Override]
    public static function keyParameterName(): string
    {
        return 'article';
    }

    #[Override]
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

    /**
     * @return array<string,string>
     */
    #[Override]
    public function attributeHints(): array
    {
        return [
            'text_raw' => Yii::t('app', 'ARTICLE_HINT_TEXT'),
            'outline_raw' => Yii::t('app', 'ARTICLE_HINT_OUTLINE'),
        ];
    }

    /**
     * @return array<string,string>
     */
    #[Override]
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
            'bestowedAccessIds' => Yii::t('app', 'BESTOWED_ACCESS_IDS_WITH_VISIBILITY')
        ];
    }

    public static function allowedVisibilities(): array
    {
        return [
            Visibility::VISIBILITY_GM,
            Visibility::VISIBILITY_DESIGNATED,
            Visibility::VISIBILITY_FULL,
        ];
    }

    /**
     * @throws Exception
     */
    #[Override]
    public function afterFind(): void
    {
        $this->bestowedAccessIds = $this->bestowedList?->getBestowedUserIds() ?? [];
        if ($this->seen_pack_id) {
            $this->seenPack->recordNotification();
        }
        parent::afterFind();
    }

    #[Override]
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

    /**
     * @throws Exception
     * @throws HttpException
     */
    #[Override]
    public function beforeSave($insert): bool
    {
        if ($insert) {
            $this->key = $this->generateKey();
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

        if (empty($this->bestowed_list_id)) {
            $list = BestowedList::createList();
            $this->bestowed_list_id = $list->bestowed_list_id;
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

    /**
     * @throws Exception
     */
    #[Override]
    public function afterSave($insert, $changedAttributes): void
    {
        if (!$this->is_off_the_record_change) {
            $this->seenPack->updateRecord();
        }
        parent::afterSave($insert, $changedAttributes);
    }

    public function getBestowedList(): ActiveQuery
    {
        return $this->hasOne(BestowedList::class, ['bestowed_list_id' => 'bestowed_list_id']);
    }

    public function getDescriptionPack(): ActiveQuery
    {
        return $this->hasOne(DescriptionPack::class, ['description_pack_id' => 'description_pack_id']);
    }

    #[Override]
    public function getEpic(): ActiveQuery
    {
        return $this->hasOne(Epic::class, ['epic_id' => 'epic_id']);
    }

    #[Override]
    public function getName(): string
    {
        return $this->title;
    }

    public function getOutlineWordCount(): int
    {
        return StringHelper::countWords($this->outline_raw ?? '');
    }

    public function getTextWordCount(): int
    {
        return StringHelper::countWords($this->text_raw ?? '');
    }

    public function getOutlinedFormatted(): string
    {
        return $this->formatText($this->outline_ready ?? $this->outline_raw, false, false);
    }

    public function getTextFormatted(): string
    {
        return $this->formatText($this->text_ready ?? $this->text_raw, true, false);
    }

    public function getTextFormattedForOperator(): string
    {
        return $this->processSecretTagsForOperator($this->getTextFormatted());
    }

    public function getTextFormattedForUser(): string
    {
        return $this->processSecretTagsForUser($this->getTextFormatted());
    }

    #[Override]
    public function recordSighting(): bool
    {
        return $this->seenPack->recordSighting();
    }

    #[Override]
    public function recordNotification(): bool
    {
        return $this->seenPack->recordNotification();
    }

    #[Override]
    public function showSightingStatus(): string
    {
        return $this->seenPack->getStatusForCurrentUser();
    }

    #[Override]
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
        $visibility = $this->getVisibility();
        $userControl = $this->canUserControlYou();

        return self::canUserViewInEpic($this->epic) &&
            ($visibility !== Visibility::VISIBILITY_GM || $userControl) &&
            (
                $visibility !== Visibility::VISIBILITY_DESIGNATED ||
                $userControl ||  // free pass on designated for operators
                $this->bestowedList->hasBestowedFor(Yii::$app->user->getId()) // is user on the list?
            );
    }

    static public function throwExceptionAboutCreate(): void
    {
        self::thrownExceptionAbout(Yii::t('app', 'NO_RIGHTS_TO_CREATE_ARTICLE'));
    }

    static public function throwExceptionAboutControl(): void
    {
        self::thrownExceptionAbout(Yii::t('app', 'NO_RIGHT_TO_CONTROL_ARTICLE'));
    }

    public static function throwExceptionAboutIndex(): void
    {
        self::thrownExceptionAbout(Yii::t('app', 'NO_RIGHTS_TO_LIST_ARTICLE'));
    }

    public static function throwExceptionAboutView(): void
    {
        self::thrownExceptionAbout(Yii::t('app', 'NO_RIGHT_TO_VIEW_ARTICLE'));
    }
}
