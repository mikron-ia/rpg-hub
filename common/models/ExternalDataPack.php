<?php

namespace common\models;

use common\models\core\Visibility;
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
            [['class'], 'required'],
            [['class'], 'string', 'max' => 20],
        ];
    }

    public function attributeLabels()
    {
        return [
            'external_data_pack_id' => Yii::t('external', 'EXTERNAL_DATA_PACK_ID'),
            'class' => Yii::t('external', 'EXTERNAL_DATA_PACK_CLASS'),
        ];
    }

    /**
     * @param string $className
     * @return ExternalDataPack
     */
    static public function create($className):ExternalDataPack
    {
        $pack = new ExternalDataPack();
        $pack->class = $className;

        $pack->save();
        $pack->refresh();

        return $pack;
    }

    /**
     * @param string $code
     * @return ExternalData
     */
    public function getExternalDataByCode(string $code)
    {
        return ExternalData::findOne([
            'external_data_pack_id' => $this->external_data_pack_id,
            'code' => $code
        ]);
    }

    /**
     * Saves external data - if absent, adds; if present, updates
     * @param string $code
     * @param array|string $data
     * @return bool
     */
    public function saveExternalData(string $code, $data):bool
    {
        /* @var $externalData ExternalData|null */
        $externalData = ExternalData::findOne([
            'code' => $code,
            'external_data_pack_id' => $this->external_data_pack_id
        ]);

        $dataFormatted = json_encode($data);

        if (!$externalData) {
            /* Create external data */
            $externalData = new ExternalData([
                'external_data_pack_id' => $this->external_data_pack_id,
                'code' => $code,
                'data' => $dataFormatted,
                'visibility' => Visibility::VISIBILITY_GM,
            ]);
            return $externalData->save();
        } else {
            /* Update external data */
            $externalData->data = $dataFormatted;
            return $externalData->save();
        }
    }
}
