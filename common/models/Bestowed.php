<?php

namespace common\models;

use Override;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Exception;

/**
 * @property int $bestowed_id
 * @property int $bestowed_list_id
 * @property int $user_id
 *
 * @property BestowedList $bestowedList
 * @property User $user
 */
class Bestowed extends ActiveRecord
{
    #[Override]
    public static function tableName(): string
    {
        return 'bestowed';
    }

    #[Override]
    public function behaviors(): array
    {
        return [
            'timestampBehavior' => ['class' => TimestampBehavior::class],
        ];
    }

    public function getUser(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * @throws Exception
     */
    public static function createForList(int $userId, int $bestowedListId): bool
    {
        $participant = new Bestowed();

        $participant->bestowed_list_id = $bestowedListId;
        $participant->user_id = $userId;

        return $participant->save();
    }

    public function __toString(): string
    {
        return $this->user->username;
    }
}
