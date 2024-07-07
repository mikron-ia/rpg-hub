<?php

namespace common\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "flag".
 *
 * @property string $flag_id
 * @property string $utility_bag_id
 * @property string $type
 * @property string $status
 *
 * @property UtilityBag $utilityBag
 */
class Flag extends ActiveRecord
{
    /** @deprecated v1.2.0, use SeenPack instead */
    public const TYPE_CHANGED = 'changed';

    /** @deprecated v1.2.0, use ImportancePack instead */
    public const TYPE_IMPORTANCE_RECALCULATE = 'imp-rec';

    public static function tableName(): string
    {
        return 'flag';
    }

    public function rules(): array
    {
        return [
            [['utility_bag_id', 'type'], 'required'],
            [['utility_bag_id'], 'integer'],
            [['type'], 'string', 'max' => 8],
            [['utility_bag_id', 'type'], 'unique', 'targetAttribute' => ['utility_bag_id', 'type']],
            [
                ['utility_bag_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => UtilityBag::class,
                'targetAttribute' => ['utility_bag_id' => 'utility_bag_id']
            ],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'flag_id' => Yii::t('app', 'FLAG_ID'),
            'utility_bag_id' => Yii::t('app', 'UTILITY_BAG'),
            'type' => Yii::t('app', 'FLAG_TYPE'),
            'status' => Yii::t('app', 'FLAG_STATUS'),
        ];
    }

    public function getUtilityBag(): ActiveQuery
    {
        return $this->hasOne(UtilityBag::class, ['utility_bag_id' => 'utility_bag_id']);
    }

    /**
     * @return string[]
     */
    static public function typeNames(): array
    {
        return [
            self::TYPE_CHANGED => Yii::t('app', 'FLAG_TYPE_CHANGED'),
            self::TYPE_IMPORTANCE_RECALCULATE => Yii::t('app', 'FLAG_TYPE_IMPORTANCE_RECALCULATE'),
        ];
    }

    public function getType(): string
    {
        $names = self::typeNames();
        return isset($names[$this->type]) ? $names[$this->type] : '?';
    }

    static public function create(int $bag_id, string $flagType): bool
    {
        self::remove($bag_id, $flagType);
        return (new Flag(['utility_bag_id' => $bag_id, 'type' => $flagType]))->save();
    }

    static public function remove(int $bag_id, string $flagType): void
    {
        Flag::deleteAll(['utility_bag_id' => $bag_id, 'type' => $flagType]);
    }

    public function __toString()
    {
        return $this->getType();
    }
}
