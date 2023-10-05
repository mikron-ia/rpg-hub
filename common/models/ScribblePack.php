<?php

namespace common\models;

use common\models\core\HasEpicControl;
use common\models\core\IsEditablePack;
use common\models\tools\ToolsForSelfFillingPacks;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "scribble_pack".
 *
 * @property int $scribble_pack_id
 * @property string $class Name of class this pack belongs to; necessary for proper type assignment
 *
 * @property Character[] $characters
 * @property Group[] $groups
 * @property Scribble[] $scribbles
 */
class ScribblePack extends ActiveRecord implements IsEditablePack
{
    use ToolsForSelfFillingPacks;

    public static function tableName(): string
    {
        return 'scribble_pack';
    }

    public function rules(): array
    {
        return [
            [['class'], 'required'],
            [['class'], 'string', 'max' => 20],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'scribble_pack_id' => Yii::t('app', 'SCRIBBLE_PACK_ID'),
            'class' => Yii::t('app', 'SCRIBBLE_PACK_CLASS'),
        ];
    }

    /**
     * Gets query for [[Characters]].
     *
     * @return ActiveQuery
     */
    public function getCharacters(): ActiveQuery
    {
        return $this->hasMany(Character::class, ['scribble_pack_id' => 'scribble_pack_id']);
    }

    /**
     * Gets query for [[Groups]].
     *
     * @return ActiveQuery
     */
    public function getGroups(): ActiveQuery
    {
        return $this->hasMany(Group::class, ['scribble_pack_id' => 'scribble_pack_id']);
    }

    /**
     * Gets query for [[Scribbles]].
     *
     * @return ActiveQuery|ScribbleQuery
     */
    public function getScribbles()
    {
        return $this->hasMany(Scribble::class, ['scribble_pack_id' => 'scribble_pack_id']);
    }

    public static function create(string $class): ScribblePack
    {
        $pack = new ScribblePack(['class' => $class]);

        $pack->save();
        $pack->refresh();

        return $pack;
    }

    public static function find(): ScribblePackQuery
    {
        return new ScribblePackQuery(get_called_class());
    }

    public function canUserReadYou(): bool
    {
        $className = 'common\models\\' . $this->class;
        /** @var HasEpicControl $object */
        $object = ($className)::findOne(['scribble_pack_id' => $this->scribble_pack_id]);
        return $object->canUserViewYou();
    }

    public function canUserControlYou(): bool
    {
        $className = 'common\models\\' . $this->class;
        /** @var HasEpicControl $object */
        $object = ($className)::findOne(['scribble_pack_id' => $this->scribble_pack_id]);
        return $object->canUserControlYou();
    }

    public function getScribbleByUserId(int $userId): Scribble
    {
        $scribble = Scribble::findOne([
            'scribble_pack_id' => $this->scribble_pack_id,
            'user_id' => $userId,
        ]);

        if (empty($scribble)) {
            $scribble = Scribble::createEmptyForPack($userId, $this);
            $scribble->save();
            $scribble->refresh();
        }

        return $scribble;
    }
}
