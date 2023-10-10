<?php

namespace common\models;

use common\models\core\SeenStatus;
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
    public static function tableName(): string
    {
        return 'seen';
    }

    public function rules(): array
    {
        return [
            [['seen_pack_id', 'user_id', 'noted_at', 'seen_at', 'alert_threshold'], 'integer'],
            [['status'], 'string', 'max' => 16],
            [['status'], 'default', 'value' => SeenStatus::STATUS_NEW->value],
            [
                ['seen_pack_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => SeenPack::class,
                'targetAttribute' => ['seen_pack_id' => 'seen_pack_id']
            ],
        ];
    }

    public function attributeLabels(): array
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
        return $this->hasOne(SeenPack::class, ['seen_pack_id' => 'seen_pack_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getUser(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * @return string[]
     */
    static public function statusNames(): array
    {
        return [
            SeenStatus::STATUS_NEW->value => Yii::t('app', 'SEEN_STATUS_NEW'),
            SeenStatus::STATUS_SEEN->value => Yii::t('app', 'SEEN_STATUS_SEEN'),
            SeenStatus::STATUS_UPDATED->value => Yii::t('app', 'SEEN_STATUS_UPDATED'),
        ];
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        $names = self::statusNames();
        return $names[$this->status] ?? '';
    }

    public function getCSS(): string
    {
        return $this->getSeenStatus()->statusCSS();
    }

    public function getSeenStatus(): SeenStatus
    {
        return SeenStatus::from($this->status);
    }

    public function setSeenStatus(SeenStatus $seenStatus): void
    {
        $this->status = $seenStatus->value;
    }

    /**
     * Sets the status unless the existing one is already newer
     *
     * @param SeenStatus $seenStatus
     * @return void
     */
    public function setSeenStatusMax(SeenStatus $seenStatus): void
    {
        if ($this->getSeenStatus()->isNewerThanMe($seenStatus)) {
            $this->setSeenStatus($seenStatus);
        }
    }
}
