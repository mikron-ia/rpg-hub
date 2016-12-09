<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "seen".
 *
 * @property string $seen_id
 * @property string $seen_pack_id
 * @property string $user_id
 * @property string $noted_at
 * @property string $seen_at
 * @property string $status
 * @property integer $alert_threshold
 *
 * @property SeenPack $seenPack
 */
class Seen extends ActiveRecord
{
    public static function tableName()
    {
        return 'seen';
    }

    public function rules()
    {
        return [
            [['seen_pack_id', 'user_id', 'noted_at', 'seen_at', 'alert_threshold'], 'integer'],
            [['status'], 'string', 'max' => 16],
            [
                ['seen_pack_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => SeenPack::className(),
                'targetAttribute' => ['seen_pack_id' => 'seen_pack_id']
            ],
        ];
    }

    public function attributeLabels()
    {
        return [
            'seen_id' => Yii::t('app', 'Seen ID'),
            'seen_pack_id' => Yii::t('app', 'Seen Pack ID'),
            'user_id' => Yii::t('app', 'User ID'),
            'noted_at' => Yii::t('app', 'Noted At'),
            'seen_at' => Yii::t('app', 'Seen At'),
            'status' => Yii::t('app', 'Status'),
            'alert_threshold' => Yii::t('app', 'Alert Threshold'),
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getSeenPack()
    {
        return $this->hasOne(SeenPack::className(), ['seen_pack_id' => 'seen_pack_id']);
    }
}
