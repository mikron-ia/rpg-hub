<?php

namespace common\models;

use common\components\ImportanceCalculator;
use common\components\ImportanceParametersDto;
use DateTimeImmutable;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "importance".
 *
 * @property string $importance_id
 * @property string $importance_pack_id
 * @property string $user_id
 * @property integer $importance
 *
 * @property ImportancePack $importancePack
 * @property User $user
 */
class Importance extends ActiveRecord
{
    public static function tableName(): string
    {
        return 'importance';
    }

    public function rules(): array
    {
        return [
            [['importance_pack_id', 'user_id', 'importance'], 'integer'],
            [
                ['importance_pack_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => ImportancePack::class,
                'targetAttribute' => ['importance_pack_id' => 'importance_pack_id']
            ],
            [
                ['user_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => User::class,
                'targetAttribute' => ['user_id' => 'id']
            ],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'importance_id' => Yii::t('app', 'IMPORTANCE_ID'),
            'importance_pack_id' => Yii::t('app', 'IMPORTANCE_PACK'),
            'user_id' => Yii::t('app', 'IMPORTANCE_USER'),
            'importance' => Yii::t('app', 'IMPORTANCE_VALUE'),
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getImportancePack(): ActiveQuery
    {
        return $this->hasOne(ImportancePack::class, ['importance_pack_id' => 'importance_pack_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getUser(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public static function createEmptyForPack(int $userId, ImportancePack $pack): self
    {
        $object = new Importance();
        $object->user_id = $userId;
        $object->importance_pack_id = $pack->importance_pack_id;
        $object->importance = 0;

        return $object;
    }

    private function calculate(): int
    {
        return (new ImportanceCalculator(ImportanceParametersDto::create(Yii::$app->params['importance'])))
            ->calculate($this->importancePack->getControllingObject(), $this->user, new DateTimeImmutable());
    }

    public function calculateAndSave(): bool
    {
        $this->importance = $this->calculate();
        return $this->save();
    }
}
