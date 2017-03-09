<?php

namespace common\models;

use common\behaviours\PerformedActionBehavior;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii2tech\ar\position\PositionBehavior;

/**
 * This is the model class for table "game".
 *
 * @property string $game_id
 * @property string $epic_id
 * @property string $time
 * @property string $status
 * @property integer $position
 * @property string $details
 * @property string $note
 *
 * @property Epic $epic
 */
class Game extends ActiveRecord
{
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
            [['details', 'note'], 'string'],
            [['time'], 'string', 'max' => 255],
            [['status'], 'string', 'max' => 20],
            [['epic_id'], 'exist', 'skipOnError' => true, 'targetClass' => Epic::className(), 'targetAttribute' => ['epic_id' => 'epic_id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'game_id' => Yii::t('app', 'GAME_ID'),
            'epic_id' => Yii::t('app', 'LABEL_EPIC'),
            'time' => Yii::t('app', 'GAME_TIME'),
            'status' => Yii::t('app', 'GAME_STATUS'),
            'position' => Yii::t('app', 'GAME_POSITION'),
            'details' => Yii::t('app', 'GAME_DETAILS'),
            'note' => Yii::t('app', 'GAME_POSITION'),
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
    static public function statusNames():array
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
    static public function statusClasses():array
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
    public function getStatus():string
    {
        $names = self::statusNames();
        return isset($names[$this->status]) ? $names[$this->status] : '?';
    }

    /**
     * @return string
     */
    public function getStatusClass():string
    {
        $names = self::statusClasses();
        return isset($names[$this->status]) ? $names[$this->status] : '';
    }
}
