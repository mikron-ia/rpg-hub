<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "importance_pack".
 *
 * @property string $importance_pack_id
 * @property string $class
 *
 * @property Character[] $characters
 * @property Group[] $groups
 * @property Importance[] $importances
 */
class ImportancePack extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'importance_pack';
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
            'importance_pack_id' => Yii::t('app', 'IMPORTANCE_PACK_ID'),
            'class' => Yii::t('app', 'IMPORTANCE_CLASS'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCharacters()
    {
        return $this->hasMany(Character::className(), ['importance_pack_id' => 'importance_pack_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGroups()
    {
        return $this->hasMany(Group::className(), ['importance_pack_id' => 'importance_pack_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getImportances()
    {
        return $this->hasMany(Importance::className(), ['importance_pack_id' => 'importance_pack_id']);
    }

    /**
     * @param string $class
     * @return ImportancePack
     */
    public static function create(string $class):ImportancePack
    {
        $pack = new ImportancePack(['class' => $class]);

        $pack->save();
        $pack->refresh();

        return $pack;
    }
}
