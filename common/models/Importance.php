<?php

namespace common\models;

use common\models\core\ImportanceCategory;
use Yii;

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
class Importance extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'importance';
    }

    public function rules()
    {
        return [
            [['importance_pack_id', 'user_id', 'importance'], 'integer'],
            [
                ['importance_pack_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => ImportancePack::className(),
                'targetAttribute' => ['importance_pack_id' => 'importance_pack_id']
            ],
            [
                ['user_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => User::className(),
                'targetAttribute' => ['user_id' => 'id']
            ],
        ];
    }

    public function attributeLabels()
    {
        return [
            'importance_id' => Yii::t('app', 'IMPORTANCE_ID'),
            'importance_pack_id' => Yii::t('app', 'IMPORTANCE_PACK'),
            'user_id' => Yii::t('app', 'IMPORTANCE_USER'),
            'importance' => Yii::t('app', 'IMPORTANCE_VALUE'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getImportancePack()
    {
        return $this->hasOne(ImportancePack::className(), ['importance_pack_id' => 'importance_pack_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * Calculates the importance value
     * @return int
     */
    private function calculate(): int
    {
        $measuredObject = $this->importancePack->getControllingObject();

        $valueFromSeen = $this->determineValueBasedOnSeen($measuredObject->getSeenStatusForUser($this->user->id));
        $valueFromCategory = $this->determineValueBasedOnImportanceCategory($measuredObject->getImportanceCategoryCode());
        $valueFromLastModified = $this->determineValueBasedOnDate($measuredObject->getLastModified(), 8);

        return $valueFromLastModified + $valueFromCategory + $valueFromSeen;
    }

    /**
     * Recalculates the importance object and saves the new value
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
        switch ($seen) {
            case 'new' :
                $result = 128;
                break;
            case 'updated' :
                $result = 64;
                break;
            default:
                $result = 0;
                break;
        }

        return $result;
    }

    /**
     * @param string $importanceCategory Importance category code
     * @return int
     */
    private function determineValueBasedOnImportanceCategory(string $importanceCategory)
    {
        switch ($importanceCategory) {
            case ImportanceCategory::IMPORTANCE_EXTREME :
                $result = 64;
                break;
            case ImportanceCategory::IMPORTANCE_HIGH:
                $result = 32;
                break;
            case ImportanceCategory::IMPORTANCE_MEDIUM:
                $result = 16;
                break;
            case ImportanceCategory::IMPORTANCE_LOW:
                $result = 8;
                break;
            case ImportanceCategory::IMPORTANCE_NONE:
                $result = 0;
                break;
            default:
                $result = 0;
                break;
        }

        return $result;
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
     * @param \DateTimeImmutable $date Date of event
     * @param int $topValue Value to assign if event was less than a day ago
     * @return int
     */
    private function determineValueBasedOnDate(\DateTimeImmutable $date, int $topValue): int
    {
        $now = new \DateTimeImmutable('now');
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
