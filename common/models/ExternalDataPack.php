<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "external_data_pack".
 *
 * @property string $external_data_pack_id
 * @property string $class
 */
class ExternalDataPack extends ActiveRecord
{
    public static function tableName()
    {
        return 'external_data_pack';
    }

    public function rules()
    {
        return [
            [['class', 'name'], 'required'],
            [['class'], 'string', 'max' => 20],
        ];
    }

    public function attributeLabels()
    {
        return [
            'external_data_pack_id' => Yii::t('app', 'EXTERNAL_DATA_PACK_ID'),
            'class' => Yii::t('app', 'EXTERNAL_DATA_PACK_CLASS'),
        ];
    }
}
