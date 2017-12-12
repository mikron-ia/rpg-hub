<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii2tech\ar\position\PositionBehavior;

/**
 * This is the model class for table "point_in_time".
 *
 * @property string $point_in_time_id
 * @property string $epic_id
 * @property string $name
 * @property string $description
 * @property string $position
 *
 * @property Epic $epic
 */
class PointInTime extends ActiveRecord
{
    public static function tableName()
    {
        return 'point_in_time';
    }

    public function rules()
    {
        return [
            [['epic_id', 'name', 'position'], 'required'],
            [['epic_id', 'position'], 'integer'],
            [['name', 'description'], 'string', 'max' => 255],
            [['epic_id'], 'exist', 'skipOnError' => true, 'targetClass' => Epic::className(), 'targetAttribute' => ['epic_id' => 'epic_id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'point_in_time_id' => Yii::t('app', 'POINT_IN_TIME_ID'),
            'epic_id' => Yii::t('app', 'POINT_IN_TIME_EPIC_ID'),
            'name' => Yii::t('app', 'POINT_IN_TIME_NAME'),
            'description' => Yii::t('app', 'POINT_IN_TIME_DESCRIPTION'),
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
}
