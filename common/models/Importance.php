<?php

namespace common\models;

use common\models\core\ImportanceCategory;
use DateTimeImmutable;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "importance".
 *
 * @property string $importance_id
 * @property string $importance_pack_id
 * @property string $user_id
 * @property integer $importance
 *
 * @property ImportancePack $importancePack
 * @property User $user
 */
class Importance extends ActiveRecord
{
    public static function tableName(): string
    {
        return 'importance';
    }

    public function rules(): array
    {
        return [
            [['importance_pack_id', 'user_id', 'importance'], 'integer'],
            [
                ['importance_pack_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => ImportancePack::class,
                'targetAttribute' => ['importance_pack_id' => 'importance_pack_id']
            ],
            [
                ['user_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => User::class,
                'targetAttribute' => ['user_id' => 'id']
            ],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'importance_id' => Yii::t('app', 'IMPORTANCE_ID'),
            'importance_pack_id' => Yii::t('app', 'IMPORTANCE_PACK'),
            'user_id' => Yii::t('app', 'IMPORTANCE_USER'),
            'importance' => Yii::t('app', 'IMPORTANCE_VALUE'),
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getImportancePack(): ActiveQuery
    {
        return $this->hasOne(ImportancePack::class, ['importance_pack_id' => 'importance_pack_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getUser(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * Calculates the importance value
     *
     * @return int
     */
    private function calculate(): int
    {
        $measuredObject = $this->importancePack->getControllingObject();

        $valueFromSeen = $this->determineValueBasedOnSeen($measuredObject->getSeenStatusForUser($this->user->id));
        $valueFromCategory = $this->determineValueBasedOnImportanceCategory($measuredObject->getImportanceCategoryObject());
        $valueFromLastModified = $this->determineValueBasedOnDate($measuredObject->getLastModified(), 8);

        return $valueFromLastModified + $valueFromCategory + $valueFromSeen;
    }

    /**
     * Recalculates the importance object and saves the new value
     *
     * @return bool
     */
    public function calculateAndSave(): bool
    {
        $this->importance = $this->calculate();
        return $this->save();
    }

    /**
     * @param string $seen Sighting code
     * @return int
     */
    private function determineValueBasedOnSeen(string $seen): int
    {
        return match ($seen) {
            'new' => 128,
            'updated' => 64,
            default => 0,
        };
    }

    private function determineValueBasedOnImportanceCategory(ImportanceCategory $importanceCategory): int
    {
        return match ($importanceCategory) {
            ImportanceCategory::IMPORTANCE_EXTREME => 64,
            ImportanceCategory::IMPORTANCE_HIGH => 32,
            ImportanceCategory::IMPORTANCE_MEDIUM => 16,
            ImportanceCategory::IMPORTANCE_LOW => 8,
            ImportanceCategory::IMPORTANCE_NONE => 0,
        };
    }

    /**
     * @param bool $isAssociated Is the character associated via group with another?
     * @return int
     */
    private function determineValueBasedOnAssociation(bool $isAssociated): int
    {
        return $isAssociated ? 8 : 0;
    }

    /**
     * @param DateTimeImmutable $date Date of event
     * @param int $topValue Value to assign if event was less than a day ago
     * @return int
     */
    private function determineValueBasedOnDate(DateTimeImmutable $date, int $topValue): int
    {
        $now = new DateTimeImmutable('now');
        $difference = $date->diff($now);

        if ($difference->y > 0) {
            $result = $topValue / 8;
        } elseif ($difference->m > 0) {
            $result = $topValue / 4;
        } elseif ($difference->d > 0) {
            $result = $topValue / 2;
        } else {
            $result = $topValue;
        }

        return (int)round($result);
    }
}
