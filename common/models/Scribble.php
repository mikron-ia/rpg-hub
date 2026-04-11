<?php

namespace common\models;

use common\models\core\HasKey;
use common\models\core\HasOwner;
use common\models\tools\ToolsForEntity;
use Override;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\web\HttpException;
use yii\web\User as YiiUser;

/**
 * This is the model class for table "scribble".
 *
 * @property int $scribble_id
 * @property int|null $scribble_pack_id
 * @property string $key
 * @property int|null $user_id
 * @property int|null $favorite
 *
 * @property ScribblePack $scribblePack
 * @property User $user
 */
class Scribble extends ActiveRecord implements HasKey, HasOwner
{
    use ToolsForEntity;

    #[Override]
    public static function tableName(): string
    {
        return 'scribble';
    }

    public static function keyParameterName(): string
    {
        return 'scribble';
    }

    #[Override]
    public function rules(): array
    {
        return [
            [['scribble_pack_id', 'user_id'], 'integer'],
            [['favorite'], 'boolean'],
            [
                ['scribble_pack_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => ScribblePack::class,
                'targetAttribute' => ['scribble_pack_id' => 'scribble_pack_id'],
            ],
            [
                ['user_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => User::class,
                'targetAttribute' => ['user_id' => 'id'],
            ],
        ];
    }

    #[Override]
    public function attributeLabels(): array
    {
        return [
            'scribble_id' => Yii::t('app', 'SCRIBBLE_ID'),
            'scribble_pack_id' => Yii::t('app', 'SCRIBBLE_PACK'),
            'key' => Yii::t('app', 'SCRIBBLE_KEY'),
            'user_id' => Yii::t('app', 'USER_LABEL'),
            'favorite' => Yii::t('app', 'SCRIBBLE_IS_FAVORITE'),
        ];
    }

    /**
     * @throws HttpException
     */
    public function beforeSave($insert): bool
    {
        if ($insert) {
            $this->key = $this->generateKey();
        }

        return parent::beforeSave($insert);
    }

    public function getScribblePack(): ActiveQuery|ScribblePackQuery
    {
        return $this->hasOne(ScribblePack::class, ['scribble_pack_id' => 'scribble_pack_id']);
    }

    public function getUser(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    #[Override]
    public function isOwnedBy(User|YiiUser|null $user): bool
    {
        if (empty($user)) {
            return false;
        }

        return $this->user->getId() === $user->getId();
    }

    /**
     * @return ScribbleQuery the active query used by this AR class.
     */
    #[Override]
    public static function find(): ScribbleQuery
    {
        return new ScribbleQuery(get_called_class());
    }

    public static function createEmptyForPack(int $userId, ScribblePack $pack): Scribble
    {
        $object = new Scribble();

        $object->user_id = $userId;
        $object->scribble_pack_id = $pack->scribble_pack_id;
        $object->favorite = false;

        return $object;
    }
}
