<?php

namespace common\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

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
    public function getDescriptions()
    {
        return $this->hasMany(Description::className(), ['description_pack_id' => 'description_pack_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getPeople()
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
}
