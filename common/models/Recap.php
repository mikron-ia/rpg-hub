<?php

namespace common\models;

use Yii;
use yii\helpers\Markdown;

/**
 * This is the model class for table "recap".
 *
 * @property string $recap_id
 * @property string $epic_id
 * @property string $key
 * @property string $name
 * @property string $data
 * @property string $time
 *
 * @property Epic $epic
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
            [['epic_id', 'key', 'name', 'data', 'time'], 'required'],
            [['epic_id'], 'integer'],
            [['data'], 'string'],
            [['time'], 'safe'],
            [['key'], 'string', 'max' => 80],
            [['name'], 'string', 'max' => 120],
            [['epic_id'], 'exist', 'skipOnError' => true, 'targetClass' => Epic::className(), 'targetAttribute' => ['epic_id' => 'epic_id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'recap_id' => Yii::t('app', 'RECAP_ID'),
            'key' => Yii::t('app', 'RECAP_KEY'),
            'name' => Yii::t('app', 'RECAP_NAME'),
            'data' => Yii::t('app', 'RECAP_DATA'),
            'time' => Yii::t('app', 'RECAP_TIME'),
        ];
    }

    public function getDataFormatted()
    {
        return Markdown::process($this->data, 'gfm');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEpic()
    {
        return $this->hasOne(Epic::className(), ['epic_id' => 'epic_id']);
    }
}
