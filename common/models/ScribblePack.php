<?php

namespace common\models;

use common\models\core\HasEpicControl;
use common\models\core\IsEditablePack;
use common\models\tools\ToolsForSelfFillingPacks;
use Override;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Exception;
use yii\web\HttpException;

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

    #[Override]
    public static function tableName(): string
    {
        return 'scribble_pack';
    }

    #[Override]
    public function rules(): array
    {
        return [
            [['class'], 'required'],
            [['class'], 'string', 'max' => 20],
        ];
    }

    /**
     * @return array<string,string>
     */
    #[Override]
    public function attributeLabels(): array
    {
        return [
            'scribble_pack_id' => Yii::t('app', 'SCRIBBLE_PACK_ID'),
            'class' => Yii::t('app', 'SCRIBBLE_PACK_CLASS'),
        ];
    }

    public function getCharacters(): ActiveQuery
    {
        return $this->hasMany(Character::class, ['scribble_pack_id' => 'scribble_pack_id']);
    }

    public function getGroups(): ActiveQuery
    {
        return $this->hasMany(Group::class, ['scribble_pack_id' => 'scribble_pack_id']);
    }

    public function getScribbles(): ScribbleQuery|ActiveQuery
    {
        return $this->hasMany(Scribble::class, ['scribble_pack_id' => 'scribble_pack_id']);
    }

    /**
     * @throws Exception
     */
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

    /**
     * @throws HttpException
     */
    #[Override]
    public function canUserReadYou(): bool
    {
        $className = 'common\models\\' . $this->class;
        /** @var HasEpicControl $object */
        $object = ($className)::findOne(['scribble_pack_id' => $this->scribble_pack_id]);
        return $object->canUserViewYou();
    }

    /**
     * @throws HttpException
     */
    #[Override]
    public function canUserControlYou(): bool
    {
        $className = 'common\models\\' . $this->class;
        /** @var HasEpicControl $object */
        $object = ($className)::findOne(['scribble_pack_id' => $this->scribble_pack_id]);
        return $object->canUserControlYou();
    }

    /**
     * @throws Exception
     */
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
