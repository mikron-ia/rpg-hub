<?php

namespace common\models;

use Yii;
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
 * @property User $user
 */
class Seen extends ActiveRecord
{
    const STATUS_NEW = 'new';
    const STATUS_UPDATED = 'updated';
    const STATUS_SEEN = 'seen';

    public static function tableName()
    {
        return 'seen';
    }

    public function rules()
    {
        return [
            [['seen_pack_id', 'user_id', 'noted_at', 'seen_at', 'alert_threshold'], 'integer'],
            [['status'], 'string', 'max' => 16],
            [['status'], 'default', 'value' => Seen::STATUS_NEW],
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
            'seen_id' => Yii::t('app', 'SEEN_ID'),
            'seen_pack_id' => Yii::t('app', 'SEEN_PACK_ID'),
            'user_id' => Yii::t('app', 'USER_ID'),
            'noted_at' => Yii::t('app', 'SEEN_NOTED_AT'),
            'seen_at' => Yii::t('app', 'SEEN_SEEN_AT'),
            'status' => Yii::t('app', 'SEEN_STATUS'),
            'alert_threshold' => Yii::t('app', 'SEEN_ALERT'),
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getSeenPack(): ActiveQuery
    {
        return $this->hasOne(SeenPack::className(), ['seen_pack_id' => 'seen_pack_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getUser(): ActiveQuery
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @return string[]
     */
    static public function statusNames(): array
    {
        return [
            self::STATUS_NEW => Yii::t('app', 'SEEN_STATUS_NEW'),
            self::STATUS_SEEN => Yii::t('app', 'SEEN_STATUS_SEEN'),
            self::STATUS_UPDATED => Yii::t('app', 'SEEN_STATUS_UPDATED'),
        ];
    }

    /**
     * @return string[]
     */
    static public function statusCSS(): array
    {
        return [
            self::STATUS_NEW => 'seen-tag-new',
            self::STATUS_UPDATED => 'seen-tag-updated',
            self::STATUS_SEEN => 'seen-tag-seen',
        ];
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        $names = self::statusNames();
        return isset($names[$this->status]) ? $names[$this->status] : '';
    }

    /**
     * @return string
     */
    public function getCSS(): string
    {
        $names = self::statusCSS();
        return isset($names[$this->status]) ? $names[$this->status] : '';
    }
}
