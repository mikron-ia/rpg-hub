<?php

namespace common\models;

use Yii;

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
class Game extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'game';
    }

    /**
     * @inheritdoc
     */
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

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'game_id' => Yii::t('app', 'Game ID'),
            'epic_id' => Yii::t('app', 'Epic ID'),
            'time' => Yii::t('app', 'Time'),
            'status' => Yii::t('app', 'Status'),
            'position' => Yii::t('app', 'Position'),
            'details' => Yii::t('app', 'Details'),
            'note' => Yii::t('app', 'Note'),
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
