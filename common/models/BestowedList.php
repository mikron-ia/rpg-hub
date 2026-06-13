<?php

namespace common\models;

use common\models\core\HasKey;
use common\models\tools\ToolsForEntity;
use Override;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Exception;
use yii\web\HttpException;

/**
 * @property int $bestowed_list_id
 * @property string $key
 *
 * @property Bestowed[] $bestowed
 */
class BestowedList extends ActiveRecord implements HasKey
{
    use ToolsForEntity;

    #[Override]
    public static function tableName(): string
    {
        return 'bestowed_list';
    }

    #[Override]
    public static function keyParameterName(): string
    {
        return 'bestowedList';
    }

    #[Override]
    public function behaviors(): array
    {
        return [
            'timestampBehavior' => ['class' => TimestampBehavior::class],
        ];
    }

    /**
     * @throws HttpException
     */
    #[Override]
    public function beforeSave($insert): bool
    {
        if ($insert) {
            $this->key = $this->generateKey();
        }

        return parent::beforeSave($insert);
    }

    public function getBestowed(): ActiveQuery
    {
        return $this->hasMany(Bestowed::class, ['bestowed_list_id' => 'bestowed_list_id']);
    }

    public function getBestowedUserIds(): array
    {
        return array_column($this->bestowed, 'user_id');
    }

    /**
     * @throws Exception
     */
    public static function createList(): BestowedList
    {
        $list = new BestowedList();

        $list->save();
        $list->refresh();

        return $list;
    }

    /**
     * @throws Exception
     */
    public function updateList(array $userIds): BestowedList
    {
        $currentBestowedIds = array_column($this->bestowed, 'user_id');

        $idsToRemove = array_diff($currentBestowedIds, $userIds);
        $idsToSkip = array_intersect($userIds, $currentBestowedIds);

        Bestowed::deleteAll([
            'bestowed_list_id' => $this->bestowed_list_id,
            'user_id' => $idsToRemove,
        ]);

        foreach ($userIds as $userId) {
            if (!in_array($userId, $idsToSkip)) {
                Bestowed::createForList($userId, $this->bestowed_list_id);
            }
        }

        return $this;
    }

    public function hasBestowedFor(int $userId): bool
    {
        return Bestowed::findOne([
                'bestowed_list_id' => $this->bestowed_list_id,
                'user_id' => $userId,
            ]) !== null;
    }
}
