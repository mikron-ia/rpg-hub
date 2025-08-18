<?php

namespace common\models;

use common\behaviours\PerformedActionBehavior;
use common\models\core\HasEpicControl;
use common\models\tools\ToolsForEntity;
use Yii;
use yii\db\ActiveRecord;
use yii2tech\ar\position\PositionBehavior;

/**
 * This is the model class for table "point_in_time".
 *
 * @property string $point_in_time_id
 * @property string $epic_id
 * @property string $name
 * @property string $text_public
 * @property string $text_protected
 * @property string $text_private
 * @property string $status
 * @property string $position
 *
 * @property Epic $epic
 */
class PointInTime extends ActiveRecord implements HasEpicControl
{
    use ToolsForEntity;

    public const STATUS_RETIRED = 'retired';
    public const STATUS_ACTIVE = 'active';
    public const STATUS_FUTURE = 'future';

    public static function tableName()
    {
        return 'point_in_time';
    }

    public function rules()
    {
        return [
            [['epic_id', 'name'], 'required'],
            [['epic_id'], 'integer'],
            [['name'], 'string', 'max' => 120],
            [['text_public', 'text_protected', 'text_private'], 'string', 'max' => 255],
            [['text_public', 'text_protected', 'text_private'], 'default', 'value' => null],
            [['status'], 'string', 'max' => 20],
            [
                ['status'],
                'in',
                'range' => [PointInTime::STATUS_ACTIVE, PointInTime::STATUS_RETIRED, PointInTime::STATUS_FUTURE]
            ],
            [
                ['epic_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Epic::class,
                'targetAttribute' => ['epic_id' => 'epic_id']
            ],
        ];
    }

    /**
     * @return array<string,string>
     */
    public function attributeHints(): array
    {
        return [
            'text_public' => Yii::t('app', 'POINT_IN_TIME_HINT_TEXT_PUBLIC'),
            'text_protected' => Yii::t('app', 'POINT_IN_TIME_HINT_TEXT_PROTECTED'),
            'text_private' => Yii::t('app', 'POINT_IN_TIME_HINT_TEXT_PRIVATE'),
        ];
    }

    /**
     * @return array<string,string>
     */
    public function attributeLabels(): array
    {
        return [
            'point_in_time_id' => Yii::t('app', 'POINT_IN_TIME_ID'),
            'epic_id' => Yii::t('app', 'POINT_IN_TIME_EPIC_ID'),
            'name' => Yii::t('app', 'POINT_IN_TIME_NAME'),
            'text_public' => Yii::t('app', 'POINT_IN_TIME_NAME_PUBLIC'),
            'text_protected' => Yii::t('app', 'POINT_IN_TIME_NAME_PROTECTED'),
            'text_private' => Yii::t('app', 'POINT_IN_TIME_NAME_PRIVATE'),
            'status' => Yii::t('app', 'POINT_IN_TIME_STATUS'),
            'position' => Yii::t('app', 'POINT_IN_TIME_POSITION'),
        ];
    }

    public function behaviors()
    {
        return [
            'positionBehavior' => [
                'class' => PositionBehavior::class,
                'positionAttribute' => 'position',
                'groupAttributes' => ['epic_id'],
            ],
            'performedActionBehavior' => [
                'class' => PerformedActionBehavior::class,
                'idName' => 'point_in_time_id',
                'className' => 'PointInTime',
            ]
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEpic()
    {
        return $this->hasOne(Epic::class, ['epic_id' => 'epic_id']);
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
        self::thrownExceptionAbout(Yii::t('app', 'NO_RIGHTS_TO_CREATE_POINT_IN_TIME'));
    }

    static function throwExceptionAboutControl()
    {
        self::thrownExceptionAbout(Yii::t('app', 'NO_RIGHT_TO_CONTROL_POINT_IN_TIME'));
    }

    static function throwExceptionAboutIndex()
    {
        self::thrownExceptionAbout(Yii::t('app', 'NO_RIGHTS_TO_LIST_POINT_IN_TIME'));
    }

    static function throwExceptionAboutView()
    {
        self::thrownExceptionAbout(Yii::t('app', 'NO_RIGHT_TO_VIEW_POINT_IN_TIME'));
    }

    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * @return string[]
     */
    static public function statusNames(): array
    {
        return [
            self::STATUS_ACTIVE => Yii::t('app', 'POINT_IN_TIME_STATUS_ACTIVE'),
            self::STATUS_FUTURE => Yii::t('app', 'POINT_IN_TIME_STATUS_FUTURE'),
            self::STATUS_RETIRED => Yii::t('app', 'POINT_IN_TIME_STATUS_RETIRED'),
        ];
    }

    /**
     * @return string[]
     */
    static public function statusCSS(): array
    {
        return [
            self::STATUS_ACTIVE => 'point-in-time-status-active',
            self::STATUS_FUTURE => 'point-in-time-status-future',
            self::STATUS_RETIRED => 'point-in-time-status-retired',
        ];
    }


    /**
     * @return string
     */
    public function getStatus(): string
    {
        $names = self::statusNames();
        return isset($names[$this->status]) ? $names[$this->status] : '';
    }

    /**
     * @return string
     */
    public function getStatusCSS(): string
    {
        $names = self::statusCSS();
        return isset($names[$this->status]) ? $names[$this->status] : '';
    }
}
