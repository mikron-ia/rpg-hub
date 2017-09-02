<?php

namespace common\models;

use common\models\core\HasImportanceCategory;
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
     * @var HasImportanceCategory
     */
    private $controllingObject;

    public static function tableName()
    {
        return 'importance_pack';
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

    /**
     * @return HasImportanceCategory
     */
    public function getControllingObject():HasImportanceCategory
    {
        if (empty($this->controllingObject)) {
            $className = 'common\models\\' . $this->class;
            $this->controllingObject = ($className)::findOne(['importance_pack_id' => $this->importance_pack_id]);
        }

        return $this->controllingObject;
    }

    /**
     * @return Epic
     */
    public function getEpic():Epic
    {
        return $this->getControllingObject()->getEpic()->one();
    }

    /**
     * Recalculates pack importance objects
     * @return bool
     */
    public function recalculatePack():bool
    {
        $result = true;

        foreach ($this->importances as $importance) {
            $result = $result && $importance->recalculate();
        }

        return $result;
    }
}
