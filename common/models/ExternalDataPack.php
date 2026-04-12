<?php

namespace common\models;

use common\models\core\HasEpicControl;
use common\models\core\IsEditablePack;
use common\models\core\Visibility;
use common\models\tools\ToolsForEntity;
use Override;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\data\ActiveDataProvider;
use yii\db\ActiveRecord;
use yii\db\Exception;
use yii\web\HttpException;

/**
 * This is the model class for table "external_data_pack".
 *
 * External data are information pulled from external sources as JSON, with known, but not well-represented structure, intended for simple display only
 *
 * @property string $external_data_pack_id
 * @property string $class
 *
 * @property Epic $epic
 *
 * @method touch(string $string)
 */
class ExternalDataPack extends ActiveRecord implements IsEditablePack
{
    use ToolsForEntity;

    #[Override]
    public static function tableName(): string
    {
        return 'external_data_pack';
    }

    #[Override]
    public function rules(): array
    {
        return [
            [['class'], 'required'],
            [['class'], 'string', 'max' => 20],
        ];
    }

    #[Override]
    public function attributeLabels(): array
    {
        return [
            'external_data_pack_id' => Yii::t('external', 'EXTERNAL_DATA_PACK_ID'),
            'key' => Yii::t('external', 'EXTERNAL_DATA_PACK_KEY'),
            'class' => Yii::t('external', 'EXTERNAL_DATA_PACK_CLASS'),
        ];
    }

    #[Override]
    public function behaviors(): array
    {
        return [
            ['class' => TimestampBehavior::class],
        ];
    }

    /**
     * @throws Exception
     */
    public static function create(string $className): ExternalDataPack
    {
        $pack = new ExternalDataPack();
        $pack->class = $className;

        $pack->save();
        $pack->refresh();

        return $pack;
    }

    public function getExternalDataAll(): ActiveDataProvider
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
     */
    public function getExternalDataByCode(string $code): array
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
     *
     * @param string $code
     * @param array|string $data
     *
     * @return bool
     *
     * @throws Exception
     */
    public function saveExternalData(string $code, $data): bool
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
        } else {
            /* Update external data */
            $externalData->data = $dataFormatted;
        }

        return $externalData->save();
    }

    /**
     * @return HasEpicControl
     */
    public function getControllingObject(): HasEpicControl
    {
        $className = 'common\models\\' . $this->class;
        /** @var HasEpicControl $object */
        return ($className)::findOne(['external_data_pack_id' => $this->external_data_pack_id]);
    }

    public function getEpic(): Epic
    {
        return $this->getControllingObject()->getEpic()->one();
    }

    /**
     * @throws HttpException
     */
    public function canUserReadYou(): bool
    {
        $className = 'common\models\\' . $this->class;
        /** @var HasEpicControl $object */
        $object = ($className)::findOne(['external_data_pack_id' => $this->external_data_pack_id]);
        return $object->canUserViewYou();
    }

    /**
     * @throws HttpException
     */
    public function canUserControlYou(): bool
    {
        $className = 'common\models\\' . $this->class;
        /** @var HasEpicControl $object */
        $object = ($className)::findOne(['external_data_pack_id' => $this->external_data_pack_id]);
        return $object->canUserControlYou();
    }
}
