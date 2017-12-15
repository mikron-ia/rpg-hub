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
 * @property string $position
 *
 * @property Epic $epic
 */
class PointInTime extends ActiveRecord implements HasEpicControl
{
    use ToolsForEntity;

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
            [['epic_id'], 'exist', 'skipOnError' => true, 'targetClass' => Epic::className(), 'targetAttribute' => ['epic_id' => 'epic_id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'point_in_time_id' => Yii::t('app', 'POINT_IN_TIME_ID'),
            'epic_id' => Yii::t('app', 'POINT_IN_TIME_EPIC_ID'),
            'name' => Yii::t('app', 'POINT_IN_TIME_NAME'),
            'text_public' => Yii::t('app', 'POINT_IN_TIME_NAME_PUBLIC'),
            'text_protected' => Yii::t('app', 'POINT_IN_TIME_NAME_PROTECTED'),
            'text_private' => Yii::t('app', 'POINT_IN_TIME_NAME_PRIVATE'),
            'position' => Yii::t('app', 'POINT_IN_TIME_POSITION'),
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
        return $this->hasOne(Epic::className(), ['epic_id' => 'epic_id']);
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
}
