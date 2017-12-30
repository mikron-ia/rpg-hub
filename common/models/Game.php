<?php

namespace common\models;

use common\behaviours\PerformedActionBehavior;
use common\models\core\HasEpicControl;
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
 * @property string $status
 * @property integer $position
 * @property string $notes
 * @property string $utility_bag_id
 *
 * @property string $notesFormatted
 *
 * @property Epic $epic
 * @property UtilityBag $utilityBag
 */
class Game extends ActiveRecord implements HasEpicControl
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

    public static function tableName()
    {
        return 'game';
    }

    public function rules()
    {
        return [
            [['epic_id'], 'required'],
            [['epic_id', 'position'], 'integer'],
            [['notes'], 'string'],
            [['basics'], 'string', 'max' => 255],
            [['status'], 'string', 'max' => 20],
            [['status'], 'in', 'range' => array_keys(Game::statusNames())],
            [
                ['epic_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Epic::className(),
                'targetAttribute' => ['epic_id' => 'epic_id']
            ],
        ];
    }

    public function attributeLabels()
    {
        return [
            'game_id' => Yii::t('app', 'GAME_ID'),
            'epic_id' => Yii::t('app', 'LABEL_EPIC'),
            'basics' => Yii::t('app', 'GAME_BASICS'),
            'time' => Yii::t('app', 'GAME_TIME'),
            'status' => Yii::t('app', 'GAME_STATUS'),
            'position' => Yii::t('app', 'GAME_POSITION'),
            'notes' => Yii::t('app', 'GAME_NOTES'),
            'utility_bag_id' => Yii::t('app', 'UTILITY_BAG'),
        ];
    }

    public function beforeSave($insert)
    {
        if (empty($this->utility_bag_id)) {
            $pack = UtilityBag::create('Game');
            $this->utility_bag_id = $pack->utility_bag_id;
        }

        return parent::beforeSave($insert);
    }

    public function behaviors()
    {
        return [
            'positionBehavior' => [
                'class' => PositionBehavior::className(),
                'positionAttribute' => 'position',
                'groupAttributes' => ['epic_id'],
            ],
            'performedActionBehavior' => [
                'class' => PerformedActionBehavior::className(),
                'idName' => 'game_id',
                'className' => 'Game',
            ],
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getEpic()
    {
        return $this->hasOne(Epic::className(), ['epic_id' => 'epic_id']);
    }

    /**
     * @return string[]
     */
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

    /**
     * @return string[]
     */
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

    /**
     * @return string
     */
    public function getStatus(): string
    {
        $names = self::statusNames();
        return isset($names[$this->status]) ? $names[$this->status] : '?';
    }

    /**
     * @return string
     */
    public function getStatusClass(): string
    {
        $names = self::statusClasses();
        return isset($names[$this->status]) ? $names[$this->status] : '';
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

    /**
     * @return string|null
     */
    public function getNotesFormatted()
    {
        return Markdown::process(Html::encode($this->notes), 'gfm');
    }
}
