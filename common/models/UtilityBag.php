<?php

namespace common\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

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
    public static function tableName()
    {
        return 'utility_bag';
    }

    public function rules()
    {
        return [
            [['class'], 'required'],
            [['class'], 'string', 'max' => 20],
        ];
    }

    public function attributeLabels()
    {
        return [
            'utility_bag_id' => Yii::t('app', 'UTILITY_BAG_ID'),
            'class' => Yii::t('app', 'UTILITY_BAG_CLASS'),
        ];
    }

    /**
     * @param string $class
     * @return UtilityBag
     */
    public static function create(string $class): UtilityBag
    {
        $bag = new UtilityBag(['class' => $class]);

        $bag->save();
        $bag->refresh();

        return $bag;
    }

    /**
     * @return ActiveQuery
     */
    public function getFlags()
    {
        return $this->hasMany(Flag::class, ['utility_bag_id' => 'utility_bag_id']);
    }

    public function setFlag(string $flag): bool
    {
        return Flag::create($this->utility_bag_id, $flag);
    }

    public function removeFlag(string $flag): bool
    {
        return Flag::remove($this->utility_bag_id, $flag);
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
     */
    public function flagForImportanceRecalculation(): bool
    {
        return $this->setFlag(Flag::TYPE_IMPORTANCE_RECALCULATE);
    }
}
