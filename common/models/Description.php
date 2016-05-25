<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "description".
 *
 * @property string $description_id
 * @property string $description_pack_id
 * @property string $title
 * @property string $code
 * @property string $public_text
 * @property string $private_text
 * @property string $lang
 *
 * @property DescriptionPack $descriptionPack
 */
class Description extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'description';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['description_pack_id', 'title', 'code', 'public_text', 'private_text', 'lang'], 'required'],
            [['description_pack_id'], 'integer'],
            [['public_text', 'private_text'], 'string'],
            [['title'], 'string', 'max' => 80],
            [['code'], 'string', 'max' => 40],
            [['lang'], 'string', 'max' => 8],
            [['description_pack_id'], 'exist', 'skipOnError' => true, 'targetClass' => DescriptionPack::className(), 'targetAttribute' => ['description_pack_id' => 'description_pack_id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'description_id' => Yii::t('app', 'Description ID'),
            'description_pack_id' => Yii::t('app', 'Description Pack ID'),
            'title' => Yii::t('app', 'Title'),
            'code' => Yii::t('app', 'Code'),
            'public_text' => Yii::t('app', 'Public Text'),
            'private_text' => Yii::t('app', 'Private Text'),
            'lang' => Yii::t('app', 'Lang'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDescriptionPack()
    {
        return $this->hasOne(DescriptionPack::className(), ['description_pack_id' => 'description_pack_id']);
    }
}
