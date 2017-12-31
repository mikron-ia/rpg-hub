<?php

namespace common\models;

use Yii;
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
     * @return \yii\db\ActiveQuery
     */
    public function getFlags()
    {
        return $this->hasMany(Flag::className(), ['utility_bag_id' => 'utility_bag_id']);
    }
}
