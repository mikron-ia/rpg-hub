<?php

namespace common\models;

use common\models\core\HasOwner;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "scribble".
 *
 * @property int $scribble_id
 * @property int|null $scribble_pack_id
 * @property int|null $user_id
 * @property int|null $favorite
 *
 * @property ScribblePack $scribblePack
 * @property User $user
 */
class Scribble extends ActiveRecord implements HasOwner
{
    public static function tableName(): string
    {
        return 'scribble';
    }

    public function rules(): array
    {
        return [
            [['scribble_pack_id', 'user_id'], 'integer'],
            [['favorite'], 'boolean'],
            [['scribble_pack_id'], 'exist', 'skipOnError' => true, 'targetClass' => ScribblePack::class, 'targetAttribute' => ['scribble_pack_id' => 'scribble_pack_id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'scribble_id' => Yii::t('app', 'SCRIBBLE_ID'),
            'scribble_pack_id' => Yii::t('app', 'SCRIBBLE_PACK'),
            'user_id' => Yii::t('app', 'USER_LABEL'),
            'favorite' => Yii::t('app', 'SCRIBBLE_IS_FAVORITE'),
        ];
    }

    /**
     * Gets query for [[ScribblePack]].
     *
     * @return ActiveQuery|ScribblePackQuery
     */
    public function getScribblePack(): ActiveQuery|ScribblePackQuery
    {
        return $this->hasOne(ScribblePack::class, ['scribble_pack_id' => 'scribble_pack_id']);
    }

    /**
     * Gets query for [[User]].
     *
     * @return ActiveQuery
     */
    public function getUser(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public function isOwnedBy(User|\yii\web\User|null $user): bool
    {
        if (empty($user)) {
            return false;
        }

        return $this->user->getId() === $user->getId();
    }

    /**
     * @return ScribbleQuery the active query used by this AR class.
     */
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
