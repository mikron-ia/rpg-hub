<?php

namespace common\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Exception;

/**
 * This is the model class for table "utility_bag".
 *
 * @property string $utility_bag_id
 * @property string $class Name of class this pack belongs to; necessary for proper type assignment
 *
 * @property Flag[] $flags
 */
class UtilityBag extends ActiveRecord
{
    public static function tableName(): string
    {
        return 'utility_bag';
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
            'utility_bag_id' => Yii::t('app', 'UTILITY_BAG_ID'),
            'class' => Yii::t('app', 'UTILITY_BAG_CLASS'),
        ];
    }

    /**
     * @param string $class
     *
     * @return UtilityBag
     *
     * @throws Exception
     */
    public static function create(string $class): UtilityBag
    {
        $bag = new UtilityBag(['class' => $class]);

        $bag->save();
        $bag->refresh();

        return $bag;
    }

    public function getFlags(): ActiveQuery
    {
        return $this->hasMany(Flag::class, ['utility_bag_id' => 'utility_bag_id']);
    }

    public function setFlag(string $flag): bool
    {
        return Flag::create($this->utility_bag_id, $flag);
    }

    public function removeFlag(string $flag): void
    {
        Flag::remove($this->utility_bag_id, $flag);
    }

    /**
     * @return bool
     */
    public function flagAsChanged(): bool
    {
        return $this->setFlag(Flag::TYPE_CHANGED);
    }

    /**
     * @return bool
     *
     * @deprecated v1.2.0, use ImportancePack->flagForRecalculation() instead
     */
    public function flagForImportanceRecalculation(): bool
    {
        return $this->setFlag(Flag::TYPE_IMPORTANCE_RECALCULATE);
    }
}
