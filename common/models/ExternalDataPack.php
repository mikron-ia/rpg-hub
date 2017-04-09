<?php

namespace common\models;

use common\models\core\HasEpicControl;
use common\models\core\IsPack;
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
 *
 * @property Epic $epic
 */
class ExternalDataPack extends ActiveRecord implements IsPack
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
            'visibility' => Visibility::determineVisibilityVector($this->epic),
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
            'visibility' => Visibility::determineVisibilityVector($this->epic),
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

    /**
     * @return HasEpicControl
     */
    public function getControllingObject():HasEpicControl
    {
        $className = 'common\models\\' . $this->class;
        /** @var HasEpicControl $object */
        return ($className)::findOne(['description_pack_id' => $this->external_data_pack_id]);
    }

    /**
     * @return Epic
     */
    public function getEpic():Epic
    {
        return $this->getControllingObject()->getEpic()->one();
    }

    public function canUserReadYou():bool
    {
        $className = 'common\models\\' . $this->class;
        /** @var HasEpicControl $object */
        $object = ($className)::findOne(['external_data_pack_id' => $this->external_data_pack_id]);
        return $object->canUserViewYou();
    }

    public function canUserControlYou():bool
    {
        $className = 'common\models\\' . $this->class;
        /** @var HasEpicControl $object */
        $object = ($className)::findOne(['external_data_pack_id' => $this->external_data_pack_id]);
        return $object->canUserControlYou();
    }
}
