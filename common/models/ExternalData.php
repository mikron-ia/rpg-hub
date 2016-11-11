<?php

namespace common\models;

use common\models\core\Visibility;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "external_data".
 *
 * External data are information pulled from external sources as JSON, with known, but not well-represented structure, intended for simple display only
 *
 * @property string $external_data_id
 * @property string $external_data_pack_id
 * @property string $code
 * @property string $data
 * @property string $visibility
 *
 * @property ExternalDataPack $externalDataPack
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

    /**
     * @return ActiveQuery
     */
    public function getExternalDataPack()
    {
        return $this->hasOne(ExternalDataPack::className(), ['external_data_pack_id' => 'external_data_pack_id']);
    }

    /**
     * @return string
     */
    public function getVisibility():string
    {
        $visibility = Visibility::create($this->visibility);
        return $visibility->getName();
    }
}
