<?php

namespace common\models;

use common\models\core\HasImportance;
use common\models\core\IsSelfFillingPack;
use common\models\exceptions\InvalidBackendConfigurationException;
use common\models\tools\ToolsForSelfFillingPacks;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Exception;

/**
 * This is the model class for table "importance_pack".
 *
 * @property string $importance_pack_id
 * @property string $class
 * @property bool $flagged
 *
 * @property Character[] $characters
 * @property Group[] $groups
 * @property Importance[] $importances
 */
class ImportancePack extends ActiveRecord implements IsSelfFillingPack
{
    use ToolsForSelfFillingPacks;

    private HasImportance $controllingObject;

    public static function tableName(): string
    {
        return 'importance_pack';
    }

    public function rules(): array
    {
        return [
            [['class'], 'required'],
            [['class'], 'string', 'max' => 20],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'importance_pack_id' => Yii::t('app', 'IMPORTANCE_PACK_ID'),
            'class' => Yii::t('app', 'IMPORTANCE_CLASS'),
            'flagged' => Yii::t('app', 'IMPORTANCE_RECALCULATION_FLAG'),
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getCharacters(): ActiveQuery
    {
        return $this->hasMany(Character::class, ['importance_pack_id' => 'importance_pack_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getGroups(): ActiveQuery
    {
        return $this->hasMany(Group::class, ['importance_pack_id' => 'importance_pack_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getImportances(): ActiveQuery
    {
        return $this->hasMany(Importance::class, ['importance_pack_id' => 'importance_pack_id']);
    }

    /**
     * @param string $class
     *
     * @return ImportancePack
     * @throws Exception
     */
    public static function create(string $class): ImportancePack
    {
        $pack = new ImportancePack([
            'class' => $class,
            'flagged' => false,
        ]);

        $pack->save();
        $pack->refresh();

        return $pack;
    }

    public function getControllingObject(): HasImportance
    {
        if (empty($this->controllingObject)) {
            $className = 'common\models\\' . $this->class;
            $this->controllingObject = ($className)::findOne(['importance_pack_id' => $this->importance_pack_id]);
        }

        return $this->controllingObject;
    }

    public function getEpic(): Epic
    {
        return $this->getControllingObject()->getEpic()->one();
    }

    public function createEmptyContent(int $userId): Importance
    {
        return Importance::createEmptyForPack($userId, $this);
    }

    /**
     * Recalculates pack importance objects
     *
     * @throws Exception
     * @throws InvalidBackendConfigurationException
     */
    public function recalculatePack(): bool
    {
        $result = $this->createAbsentRecords(
            $this->getEpic(),
            $this,
            Importance::findAll(['importance_pack_id' => $this->importance_pack_id])
        );

        foreach ($this->importances as $importance) {
            $result = $result && $importance->calculateAndSave();
        }

        return $result;
    }

    /**
     * Flags the pack for recalculation
     *
     * @throws Exception
     */
    public function flagForRecalculation(): bool
    {
        $this->flagged = true;
        return $this->save();
    }

    /**
     * Removes the recalculation flag
     *
     * @throws Exception
     */
    public function unflagForRecalculation(): bool
    {
        $this->flagged = false;
        return $this->save();
    }

    /**
     * Reports whether the pack is flagged for recalculation
     */
    public function isFlaggedForRecalculation(): bool
    {
        return $this->flagged;
    }
}
