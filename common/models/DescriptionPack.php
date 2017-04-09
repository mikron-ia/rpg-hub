<?php

namespace common\models;

use common\models\core\HasEpicControl;
use common\models\core\IsPack;
use common\models\core\Language;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\web\HttpException;

/**
 * This is the model class for table "description_pack".
 *
 * @property string $description_pack_id
 * @property string $class
 *
 * @property Description[] $descriptions
 * @property Character[] $people
 * @property Epic $epic
 */
final class DescriptionPack extends ActiveRecord implements Displayable, IsPack
{
    public static function tableName()
    {
        return 'description_pack';
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
            'description_pack_id' => Yii::t('app', 'DESCRIPTION_PACK_ID'),
            'class' => Yii::t('app', 'DESCRIPTION_PACK_CLASS'),
        ];
    }

    public function behaviors()
    {
        return [
            ['class' => TimestampBehavior::className()],
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getDescriptions():ActiveQuery
    {
        return $this->hasMany(Description::className(), ['description_pack_id' => 'description_pack_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getPeople():ActiveQuery
    {
        return $this->hasMany(Character::className(), ['description_pack_id' => 'description_pack_id']);
    }


    /**
     * @param string $className
     * @return DescriptionPack
     */
    static public function create($className)
    {
        $pack = new DescriptionPack();
        $pack->class = $className;

        $pack->save();
        $pack->refresh();

        return $pack;
    }

    public function getSimpleDataForApi()
    {
        $descriptions = [];

        foreach ($this->descriptions as $description) {
            $descriptions[] = $description->getSimpleDataForApi();
        }

        return $descriptions;
    }

    public function getCompleteDataForApi()
    {
        $descriptions = [];

        foreach ($this->descriptions as $description) {
            if ($description->isVisibleInApi()) {
                $descriptions[] = $description->getCompleteDataForApi();
            }
        }

        return $descriptions;
    }

    public function isVisibleInApi()
    {
        return true;
    }

    /**
     * @param Language $language
     * @return ActiveQuery
     */
    public function getDescriptionsInLanguage(Language $language):ActiveQuery
    {
        return DescriptionQuery::listDescriptionsInLanguage($this, $language);
    }

    /**
     * @param User $user
     * @return ActiveQuery
     */
    public function getDescriptionsInLanguageOfTheUser(User $user):ActiveQuery
    {
        $language = Language::create($user->language);
        return $this->getDescriptionsInLanguage($language);
    }

    /**
     * @return ActiveQuery
     * @throws HttpException
     */
    public function getDescriptionsInLanguageOfTheActiveUser():ActiveQuery
    {
        if (Yii::$app->user->isGuest) {
            throw new HttpException(400, Yii::t('app', 'ERROR_NO_ACTIVE_USER'));
        }

        /** @var $user User */
        $user = Yii::$app->user->identity;

        return $this->getDescriptionsInLanguageOfTheUser($user);
    }

    /**
     * @param Language $language
     * @param string $code
     * @return ActiveQuery
     */
    public function getDescriptionInLanguage(Language $language, string $code):ActiveQuery
    {
        return $this->getDescriptions()->where(['lang' => $language->language, 'code' => $code]);
    }

    /**
     * @param User $user
     * @param string $code
     * @return ActiveQuery
     */
    public function getDescriptionInLanguageOfTheUser(User $user, string $code):ActiveQuery
    {
        $language = Language::create($user->language);
        return $this->getDescriptionInLanguage($language, $code);
    }

    /**
     * @param string $code
     * @return ActiveQuery
     * @throws HttpException
     */
    public function getDescriptionInLanguageOfTheActiveUser(string $code):ActiveQuery
    {
        if (Yii::$app->user->isGuest) {
            throw new HttpException(400, Yii::t('app', 'ERROR_NO_ACTIVE_USER'));
        }

        /** @var $user User */
        $user = Yii::$app->user->identity;

        return $this->getDescriptionInLanguageOfTheUser($user, $code);
    }

    /**
     * @return HasEpicControl
     */
    public function getControllingObject():HasEpicControl
    {
        $className = 'common\models\\' . $this->class;
        /** @var HasEpicControl $object */
        return ($className)::findOne(['description_pack_id' => $this->description_pack_id]);
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
        $object = ($className)::findOne(['description_pack_id' => $this->description_pack_id]);
        return $object->canUserViewYou();
    }

    public function canUserControlYou():bool
    {
        $className = 'common\models\\' . $this->class;
        /** @var HasEpicControl $object */
        $object = ($className)::findOne(['description_pack_id' => $this->description_pack_id]);
        return $object->canUserControlYou();
    }
}
