<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "utility_bag".
 *
 * @property string $utility_bag_id
 * @property string $class
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
        $pack = new UtilityBag(['class' => $class]);

        $pack->save();
        $pack->refresh();

        return $pack;
    }
}
