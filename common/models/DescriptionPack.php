<?php

namespace common\models;

use common\models\core\HasEpicControl;
use common\models\core\HasKey;
use common\models\core\IsEditablePack;
use common\models\core\Language;
use common\models\tools\ToolsForEntity;
use Override;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Exception;
use yii\web\HttpException;

/**
 * @property string $description_pack_id
 * @property string $key
 * @property string $class
 *
 * @property Description[] $descriptions
 * @property Character[] $people
 * @property Epic $epic
 *
 * @method touch(string $string)
 */
final class DescriptionPack extends ActiveRecord implements Displayable, HasKey, IsEditablePack
{
    use ToolsForEntity;

    #[Override]
    public static function tableName(): string
    {
        return 'description_pack';
    }

    #[Override]
    public static function keyParameterName(): string
    {
        return 'descriptionPack';
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
            'description_pack_id' => Yii::t('app', 'DESCRIPTION_PACK_ID'),
            'key' => Yii::t('app', 'DESCRIPTION_PACK_KEY'),
            'class' => Yii::t('app', 'DESCRIPTION_PACK_CLASS'),
        ];
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

    #[Override]
    public function behaviors(): array
    {
        return [
            ['class' => TimestampBehavior::class],
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getDescriptions(): ActiveQuery
    {
        return $this->hasMany(Description::class, ['description_pack_id' => 'description_pack_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getPeople(): ActiveQuery
    {
        return $this->hasMany(Character::class, ['description_pack_id' => 'description_pack_id']);
    }

    /**
     * @throws Exception
     */
    public static function create(string $className): DescriptionPack
    {
        $pack = new DescriptionPack();
        $pack->class = $className;

        $pack->save();
        $pack->refresh();

        return $pack;
    }

    #[Override]
    public function getSimpleDataForApi(): array
    {
        $descriptions = [];

        foreach ($this->descriptions as $description) {
            $descriptions[] = $description->getSimpleDataForApi();
        }

        return $descriptions;
    }

    #[Override]
    public function getCompleteDataForApi(): array
    {
        $descriptions = [];

        foreach ($this->descriptions as $description) {
            if ($description->isVisibleInApi()) {
                $descriptions[] = $description->getCompleteDataForApi();
            }
        }

        return $descriptions;
    }

    #[Override]
    public function isVisibleInApi(): bool
    {
        return true;
    }

    public function getDescriptionsInLanguage(Language $language): ActiveQuery
    {
        return DescriptionQuery::listDescriptionsInLanguage($this, $language);
    }

    public function getDescriptionsVisible(): ActiveQuery
    {
        return DescriptionQuery::listDescriptions($this);
    }

    public function getDescriptionsVisibleUnexpired(): ActiveQuery
    {
        return DescriptionQuery::listDescriptionsUnexpired($this);
    }

    public function getDescriptionsInLanguageOfTheUser(User $user): ActiveQuery
    {
        $language = Language::create($user->language);
        return $this->getDescriptionsInLanguage($language);
    }

    /**
     * @throws HttpException
     */
    public function getDescriptionsInLanguageOfTheActiveUser(): ActiveQuery
    {
        if (Yii::$app->user->isGuest) {
            throw new HttpException(400, Yii::t('app', 'ERROR_NO_ACTIVE_USER'));
        }

        /** @var $user User */
        $user = Yii::$app->user->identity;

        return $this->getDescriptionsInLanguageOfTheUser($user);
    }

    public function getDescriptionInLanguage(Language $language, string $code): ActiveQuery
    {
        return $this->getDescriptions()->where(['lang' => $language->language, 'code' => $code]);
    }

    public function getDescriptionInLanguageOfTheUser(User $user, string $code): ActiveQuery
    {
        $language = Language::create($user->language);
        return $this->getDescriptionInLanguage($language, $code);
    }

    /**
     * @throws HttpException
     */
    public function getDescriptionInLanguageOfTheActiveUser(string $code): ActiveQuery
    {
        if (Yii::$app->user->isGuest) {
            throw new HttpException(400, Yii::t('app', 'ERROR_NO_ACTIVE_USER'));
        }

        /** @var $user User */
        $user = Yii::$app->user->identity;

        return $this->getDescriptionInLanguageOfTheUser($user, $code);
    }

    public function getControllingObject(): HasEpicControl
    {
        $className = 'common\models\\' . $this->class;
        /** @var HasEpicControl $object */
        return ($className)::findOne(['description_pack_id' => $this->description_pack_id]);
    }

    public function getEpic(): Epic
    {
        return $this->getControllingObject()->getEpic()->one();
    }

    /**
     * @throws HttpException
     */
    #[Override]
    public function canUserReadYou(): bool
    {
        $className = 'common\models\\' . $this->class;
        /** @var HasEpicControl $object */
        $object = ($className)::findOne(['description_pack_id' => $this->description_pack_id]);
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
        $object = ($className)::findOne(['description_pack_id' => $this->description_pack_id]);
        return $object->canUserControlYou();
    }

    /**
     * Counts every type of description once
     */
    public function getUniqueDescriptionTypesCount(): int
    {
        return count(array_reduce($this->descriptions, function (array $carry, Description $item) {
            $carry[$item->code] = true;
            return $carry;
        }, []));
    }
}
