<?php

namespace common\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "flag".
 *
 * @property string $flag_id
 * @property string $utility_bag_id
 * @property string $type
 * @property string $status
 *
 * @property UtilityBag $utilityBag
 */
class Flag extends ActiveRecord
{
    public static function tableName()
    {
        return 'flag';
    }

    public function rules()
    {
        return [
            [['utility_bag_id', 'type', 'status'], 'required'],
            [['utility_bag_id'], 'integer'],
            [['type', 'status'], 'string', 'max' => 10],
            [['utility_bag_id'], 'exist', 'skipOnError' => true, 'targetClass' => UtilityBag::className(), 'targetAttribute' => ['utility_bag_id' => 'utility_bag_id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'flag_id' => Yii::t('app', 'Flag ID'),
            'utility_bag_id' => Yii::t('app', 'Utility Bag ID'),
            'type' => Yii::t('app', 'Type'),
            'status' => Yii::t('app', 'Status'),
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getUtilityBag()
    {
        return $this->hasOne(UtilityBag::className(), ['utility_bag_id' => 'utility_bag_id']);
    }
}
