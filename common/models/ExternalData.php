<?php

namespace common\models;

use common\models\core\HasKey;
use common\models\core\HasVisibility;
use common\models\tools\ToolsForEntity;
use common\models\tools\ToolsForHasVisibility;
use Override;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\web\HttpException;

/**
 * This is the model class for table "external_data".
 *
 * External data are information pulled from external sources as JSON, with known, but not well-represented structure, intended for simple display only
 *
 * @property string $external_data_id
 * @property string $external_data_pack_id
 * @property string $key
 * @property string $code
 * @property string $data
 * @property string $visibility
 *
 * @property ExternalDataPack $externalDataPack
 */
class ExternalData extends ActiveRecord implements HasKey, HasVisibility
{
    use ToolsForEntity;
    use ToolsForHasVisibility;

    public static function tableName()
    {
        return 'external_data';
    }

    #[Override]
    public static function keyParameterName(): string
    {
        return 'externalData';
    }

    public function afterSave($insert, $changedAttributes)
    {
        if (!empty($changedAttributes)) {
            $this->externalDataPack->touch('updated_at');
        }

        parent::afterSave($insert, $changedAttributes);
    }

    /**
     * @throws HttpException
     */
    #[Override]
    public function beforeSave($insert): bool
    {
        if ($insert) {
            $this->key = $this->generateKey();
        }

        return parent::beforeSave($insert);
    }

    public function behaviors()
    {
        return [
            ['class' => TimestampBehavior::class],
            ['class' => BlameableBehavior::class],
        ];
    }

    public function rules()
    {
        return [
            [['visibility'], 'string', 'max' => 20],
            [
                ['visibility'],
                'in',
                'range' => fn() => $this->allowedVisibilitiesForValidator(),
            ],
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
    public function getExternalDataPack(): ActiveQuery
    {
        return $this->hasOne(ExternalDataPack::class, ['external_data_pack_id' => 'external_data_pack_id']);
    }
}
