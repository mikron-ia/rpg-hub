<?php

namespace common\models;

use common\behaviours\PerformedActionBehavior;
use common\models\core\HasEpicControl;
use common\models\core\HasStatus;
use common\models\tools\ToolsForEntity;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\Html;
use yii\helpers\Markdown;
use yii2tech\ar\position\PositionBehavior;

/**
 * This is the model class for table "game".
 *
 * @property string $game_id
 * @property string $epic_id
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
 */
class Game extends ActiveRecord implements HasEpicControl, HasStatus
{
    use ToolsForEntity;

    const STATUS_PROPOSED = 'proposed';       // game was entered on page; next: ANNOUNCED, PLANNED, UNPLANNED
    const STATUS_ANNOUNCED = 'announced';     // information was propagated; next: PLANNED, UNPLANNED
    const STATUS_UNPLANNED = 'unplanned';     // game failed to achieve planning stage; next: none
    const STATUS_PLANNED = 'planned';         // game is planned; next: PROGRESSING, CANCELLED
    const STATUS_CANCELLED = 'cancelled';     // plans cancelled; next: none
    const STATUS_PROGRESSING = 'progressing'; // game is in progress; next: COMPLETED, ABORTED
    const STATUS_ABORTED = 'aborted';         // game was started, but aborted; next: none
    const STATUS_COMPLETED = 'completed';     // game was completed; next: CLOSED
    const STATUS_CLOSED = 'closed';           // game was described; next: none

    public static function tableName(): string
    {
        return 'game';
    }

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
                'range' => $this->getAllowedChange(),
                'message' => Yii::t(
                    'app',
                    'EPIC_STATUS_NOT_ALLOWED {allowed}',
                    ['allowed' => implode(', ', $this->getAllowedChangeNames())]
                ),
                'on' => 'update',
            ],
            [
                ['epic_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Epic::class,
                'targetAttribute' => ['epic_id' => 'epic_id']
            ],
            [
                ['recap_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Recap::class,
                'targetAttribute' => ['recap_id' => 'recap_id']
            ],
            [
                ['utility_bag_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => UtilityBag::class,
                'targetAttribute' => ['utility_bag_id' => 'utility_bag_id']
            ],
        ];
    }

    /**
     * @return string[]
     */
    public function attributeLabels(): array
    {
        return [
            'game_id' => Yii::t('app', 'GAME_ID'),
            'epic_id' => Yii::t('app', 'LABEL_EPIC'),
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

    public function beforeSave($insert): bool
    {
        if (empty($this->utility_bag_id)) {
            $pack = UtilityBag::create('Game');
            $this->utility_bag_id = $pack->utility_bag_id;
        }

        return parent::beforeSave($insert);
    }

    public function afterSave($insert, $changedAttributes): void
    {
        $this->utilityBag->flagAsChanged();
        parent::afterSave($insert, $changedAttributes);
    }

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

    public function getEpic(): ActiveQuery
    {
        return $this->hasOne(Epic::class, ['epic_id' => 'epic_id']);
    }

    static public function statusNames(): array
    {
        return [
            self::STATUS_PROPOSED => Yii::t('app', 'GAME_STATUS_PROPOSED'),
            self::STATUS_ANNOUNCED => Yii::t('app', 'GAME_STATUS_ANNOUNCED'),
            self::STATUS_UNPLANNED => Yii::t('app', 'GAME_STATUS_UNPLANNED'),
            self::STATUS_PLANNED => Yii::t('app', 'GAME_STATUS_PLANNED'),
            self::STATUS_CANCELLED => Yii::t('app', 'GAME_STATUS_CANCELLED'),
            self::STATUS_PROGRESSING => Yii::t('app', 'GAME_STATUS_PROGRESSING'),
            self::STATUS_ABORTED => Yii::t('app', 'GAME_STATUS_ABORTED'),
            self::STATUS_COMPLETED => Yii::t('app', 'GAME_STATUS_COMPLETED'),
            self::STATUS_CLOSED => Yii::t('app', 'GAME_STATUS_CLOSED'),
        ];
    }

    static public function statusClasses(): array
    {
        return [
            self::STATUS_PROPOSED => 'game-status-proposed',
            self::STATUS_ANNOUNCED => 'game-status-announced',
            self::STATUS_UNPLANNED => 'game-status-unplanned',
            self::STATUS_PLANNED => 'game-status-planned',
            self::STATUS_CANCELLED => 'game-status-cancelled',
            self::STATUS_PROGRESSING => 'game-status-progressing',
            self::STATUS_ABORTED => 'game-status-aborted',
            self::STATUS_COMPLETED => 'game-status-completed',
            self::STATUS_CLOSED => 'game-status-closed',
        ];
    }

    public function statusAllowedChanges(): array
    {
        return [
            self::STATUS_PROPOSED => [self::STATUS_ANNOUNCED, self::STATUS_PLANNED, self::STATUS_UNPLANNED],
            self::STATUS_ANNOUNCED => [self::STATUS_PLANNED, self::STATUS_UNPLANNED],
            self::STATUS_UNPLANNED => [],
            self::STATUS_PLANNED => [self::STATUS_PROGRESSING, self::STATUS_CANCELLED],
            self::STATUS_CANCELLED => [],
            self::STATUS_PROGRESSING => [self::STATUS_COMPLETED, self::STATUS_ABORTED],
            self::STATUS_ABORTED => [],
            self::STATUS_COMPLETED => [self::STATUS_CLOSED],
            self::STATUS_CLOSED => [],
        ];
    }

    public function getStatus(): string
    {
        $names = self::statusNames();
        return $names[$this->status] ?? '?';
    }

    public function getStatusClass(): string
    {
        $names = self::statusClasses();
        return $names[$this->status] ?? '';
    }

    public function getAllowedChange(): array
    {
        return array_merge(($this->statusAllowedChanges()[$this->status] ?? []), [$this->status]);
    }

    public function getAllowedChangeNames(): array
    {
        return array_filter(self::statusNames(), function ($key) {
            return in_array($key, $this->getAllowedChange());
        }, ARRAY_FILTER_USE_KEY);
    }

    public function getRecap(): ActiveQuery
    {
        return $this->hasOne(Recap::class, ['recap_id' => 'recap_id']);
    }

    public function getUtilityBag(): ActiveQuery
    {
        return $this->hasOne(UtilityBag::class, ['utility_bag_id' => 'utility_bag_id']);
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

    static function throwExceptionAboutCreate()
    {
        self::thrownExceptionAbout(Yii::t('app', 'NO_RIGHTS_TO_CREATE_SESSION'));
    }

    static function throwExceptionAboutControl()
    {
        self::thrownExceptionAbout(Yii::t('app', 'NO_RIGHT_TO_CONTROL_SESSION'));
    }

    static function throwExceptionAboutIndex()
    {
        self::thrownExceptionAbout(Yii::t('app', 'NO_RIGHTS_TO_LIST_SESSION'));
    }

    static function throwExceptionAboutView()
    {
        self::thrownExceptionAbout(Yii::t('app', 'NO_RIGHT_TO_VIEW_SESSION'));
    }

    public function getNotesFormatted(): ?string
    {
        return Markdown::process(Html::encode($this->notes), 'gfm');
    }
}
