<?php

namespace common\models;

use common\behaviours\PerformedActionBehavior;
use common\models\core\HasEpicControl;
use common\models\core\HasKey;
use common\models\state\GameStatus;
use common\models\tools\ToolsForEntity;
use Override;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Exception;
use yii\helpers\Html;
use yii\helpers\Markdown;
use yii\web\HttpException;
use yii2tech\ar\position\PositionBehavior;

/**
 * @property int $game_id
 * @property int $epic_id
 * @property string $key
 * @property string $basics
 * @property string $planned_date
 * @property string $planned_location
 * @property string $status
 * @property int $position
 * @property string $notes
 * @property string $utility_bag_id
 *
 * @property string $notesFormatted
 *
 * @property Epic $epic
 * @property Recap $recap
 * @property UtilityBag $utilityBag
 *
 * @method movePrev()
 * @method moveNext()
 */
class Game extends ActiveRecord implements HasEpicControl, HasKey
{
    use ToolsForEntity;

    #[Override]
    public static function tableName(): string
    {
        return 'game';
    }

    public static function keyParameterName(): string
    {
        return 'game';
    }

    #[Override]
    public function rules(): array
    {
        return [
            [['epic_id'], 'required'],
            [['epic_id', 'position', 'recap_id', 'utility_bag_id'], 'integer'],
            [['planned_date'], 'safe'],
            [['planned_date', 'planned_location'], 'default', 'value' => null],
            [['notes'], 'string'],
            [['basics'], 'string', 'max' => 255],
            [['planned_location'], 'string', 'max' => 80],
            [['status'], 'string', 'max' => 20],
            [
                ['status'],
                'in',
                'range' => $this->getStatus()->getAllowedSuccessorsAsKeys(),
                'message' => Yii::t(
                    'app',
                    'GAME_STATE_NOT_ALLOWED {allowed}',
                    ['allowed' => implode(', ', $this->getStatus()->getAllowedSuccessorsAsStrings())],
                ),
                'on' => 'update',
            ],
            [
                ['epic_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Epic::class,
                'targetAttribute' => ['epic_id' => 'epic_id'],
            ],
            [
                ['recap_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Recap::class,
                'targetAttribute' => ['recap_id' => 'recap_id'],
            ],
            [
                ['utility_bag_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => UtilityBag::class,
                'targetAttribute' => ['utility_bag_id' => 'utility_bag_id'],
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
            'basics' => Yii::t('app', 'GAME_HINT_BASICS'),
            'notes' => Yii::t('app', 'GAME_HINT_NOTES'),
        ];
    }

    /**
     * @return array<string,string>
     */
    #[Override]
    public function attributeLabels(): array
    {
        return [
            'game_id' => Yii::t('app', 'GAME_ID'),
            'epic_id' => Yii::t('app', 'LABEL_EPIC'),
            'key' => Yii::t('app', 'GAME_KEY'),
            'basics' => Yii::t('app', 'GAME_BASICS'),
            'planned_date' => Yii::t('app', 'GAME_PLANNED_DATE'),
            'planned_location' => Yii::t('app', 'GAME_PLANNED_LOCATION'),
            'status' => Yii::t('app', 'GAME_STATUS'),
            'position' => Yii::t('app', 'GAME_POSITION'),
            'notes' => Yii::t('app', 'GAME_NOTES'),
            'recap_id' => Yii::t('app', 'LABEL_RECAP'),
            'utility_bag_id' => Yii::t('app', 'UTILITY_BAG'),
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

        if (empty($this->utility_bag_id)) {
            $pack = UtilityBag::create('Game');
            $this->utility_bag_id = $pack->utility_bag_id;
        }

        return parent::beforeSave($insert);
    }

    #[Override]
    public function afterSave($insert, $changedAttributes): void
    {
        parent::afterSave($insert, $changedAttributes);
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
            'performedActionBehavior' => [
                'class' => PerformedActionBehavior::class,
                'idName' => 'game_id',
                'className' => 'Game',
            ],
        ];
    }

    #[Override]
    public function getEpic(): ActiveQuery
    {
        return $this->hasOne(Epic::class, ['epic_id' => 'epic_id']);
    }

    public function getRecap(): ActiveQuery
    {
        return $this->hasOne(Recap::class, ['recap_id' => 'recap_id']);
    }

    public function getUtilityBag(): ActiveQuery
    {
        return $this->hasOne(UtilityBag::class, ['utility_bag_id' => 'utility_bag_id']);
    }

    public function getStatus(): GameStatus
    {
        return $this->status === null ? GameStatus::Proposed : GameStatus::from($this->status);
    }

    #[Override]
    static public function canUserIndexThem(): bool
    {
        return self::canUserIndexInEpic(Yii::$app->params['activeEpic']);
    }

    #[Override]
    static public function canUserCreateThem(): bool
    {
        return self::canUserCreateInEpic(Yii::$app->params['activeEpic']);
    }

    #[Override]
    public function canUserControlYou(): bool
    {
        return self::canUserControlInEpic($this->epic);
    }

    #[Override]
    public function canUserViewYou(): bool
    {
        return self::canUserViewInEpic($this->epic);
    }

    #[Override]
    public static function throwExceptionAboutCreate(): void
    {
        self::thrownExceptionAbout(Yii::t('app', 'NO_RIGHTS_TO_CREATE_SESSION'));
    }

    #[Override]
    public static function throwExceptionAboutControl(): void
    {
        self::thrownExceptionAbout(Yii::t('app', 'NO_RIGHT_TO_CONTROL_SESSION'));
    }

    #[Override]
    public static function throwExceptionAboutIndex(): void
    {
        self::thrownExceptionAbout(Yii::t('app', 'NO_RIGHTS_TO_LIST_SESSION'));
    }

    #[Override]
    public static function throwExceptionAboutView(): void
    {
        self::thrownExceptionAbout(Yii::t('app', 'NO_RIGHT_TO_VIEW_SESSION'));
    }

    public function getNotesFormatted(): ?string
    {
        return Markdown::process(Html::encode($this->notes), 'gfm');
    }
}
