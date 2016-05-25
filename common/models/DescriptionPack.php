<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "description_pack".
 *
 * @property string $description_pack_id
 * @property string $name
 *
 * @property Description[] $descriptions
 */
class DescriptionPack extends \yii\db\ActiveRecord
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
            [['name'], 'required'],
            [['name'], 'string', 'max' => 80],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'description_pack_id' => Yii::t('app', 'Description Pack ID'),
            'name' => Yii::t('app', 'Name'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDescriptions()
    {
        return $this->hasMany(Description::className(), ['description_pack_id' => 'description_pack_id']);
    }
}
