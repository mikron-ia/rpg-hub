<?php

namespace common\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

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

    /**
     * @return ActiveQuery
     */
    public function getEpic()
    {
        return $this->hasOne(Epic::className(), ['epic_id' => 'epic_id']);
    }
}
