<?php

namespace common\models;

use common\models\core\Language;
use Yii;
use yii\base\Exception;
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
 * @property Person[] $people
 */
final class DescriptionPack extends ActiveRecord implements Displayable
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'description_pack';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['class'], 'required'],
            [['class'], 'string', 'max' => 20],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'description_pack_id' => Yii::t('app', 'DESCRIPTION_PACK_ID'),
            'class' => Yii::t('app', 'DESCRIPTION_PACK_CLASS'),
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
        return $this->hasMany(Person::className(), ['description_pack_id' => 'description_pack_id']);
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

    /**
     * @inheritdoc
     */
    public function getSimpleDataForApi()
    {
        $descriptions = [];

        foreach ($this->descriptions as $description) {
            $descriptions[] = $description->getSimpleDataForApi();
        }

        return $descriptions;
    }

    /**
     * @inheritdoc
     */
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

    /**
     * @inheritdoc
     */
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
        return $this->getDescriptions()->where(['lang' => $language->language]);
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
}
