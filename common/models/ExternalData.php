<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "external_data".
 *
 * @property string $external_data_id
 * @property string $external_data_pack_id
 * @property string $code
 * @property string $data
 * @property string $visibility
 */
class ExternalData extends ActiveRecord
{
    public static function tableName()
    {
        return 'external_data';
    }

    public function rules()
    {
        return [
            [['external_data_pack_id'], 'integer'],
            [['code', 'data'], 'required'],
            [['data'], 'string'],
            [['code'], 'string', 'max' => 40],
            [['visibility'], 'string', 'max' => 20],
        ];
    }

    public function attributeLabels()
    {
        return [
            'external_data_id' => Yii::t('external', 'EXTERNAL_DATA_ID'),
            'external_data_pack_id' => Yii::t('external', 'EXTERNAL_DATA_PACK_ID'),
            'code' => Yii::t('external', 'EXTERNAL_DATA_CODE'),
            'visibility' => Yii::t('app', 'LABEL_VISIBILITY'),
            'data' => Yii::t('external', 'EXTERNAL_DATA_DATA'),
        ];
    }
}
