<?php

namespace common\models;

use common\models\core\Visibility;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\data\ActiveDataProvider;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "external_data_pack".
 *
 * External data are information pulled from external sources as JSON, with known, but not well-represented structure, intended for simple display only
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

    public function behaviors()
    {
        return [
            ['class' => TimestampBehavior::className()],
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
     * @return ActiveDataProvider
     */
    public function getExternalDataAll():ActiveDataProvider
    {
        $objectsQuery = ExternalData::find();

        $objectsQuery->where([
            'external_data_pack_id' => $this->external_data_pack_id,
            'visibility' => Visibility::determineVisibilityVector(),
        ]);

        return new ActiveDataProvider(['query' => $objectsQuery]);
    }

    /**
     * Provides content of the desired ExternalData object in a form of an array
     * @param string $code
     * @return array
     */
    public function getExternalDataByCode(string $code):array
    {
        $object = ExternalData::findOne([
            'external_data_pack_id' => $this->external_data_pack_id,
            'code' => $code,
            'visibility' => Visibility::determineVisibilityVector(),
        ]);

        if ($object) {
            return json_decode($object->data, true);
        } else {
            return [];
        }
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
