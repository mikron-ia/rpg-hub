<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "recap".
 *
 * @property string $recap_id
 * @property string $key
 * @property string $name
 * @property string $data
 * @property string $time
 */
class Recap extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'recap';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['key', 'name', 'data', 'time'], 'required'],
            [['data'], 'string'],
            [['time'], 'safe'],
            [['key'], 'string', 'max' => 80],
            [['name'], 'string', 'max' => 120]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'recap_id' => Yii::t('app', 'Recap ID'),
            'key' => Yii::t('app', 'Key'),
            'name' => Yii::t('app', 'Name'),
            'data' => Yii::t('app', 'Data'),
            'time' => Yii::t('app', 'Time'),
        ];
    }
}
