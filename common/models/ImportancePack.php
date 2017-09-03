<?php

namespace common\models;

use common\models\core\HasImportance;
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
     * @var HasImportance
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
     * @return HasImportance
     */
    public function getControllingObject():HasImportance
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
        $result = $this->createAbsentImportanceObjects();

        foreach ($this->importances as $importance) {
            $result = $result && $importance->calculateAndSave();
        }

        return $result;
    }

    /**
     * Creates new Importance objects for users that do not have them
     * @return bool
     */
    private function createAbsentImportanceObjects()
    {
        /** @var User[] $users */
        $users = User::findAll(['status' => User::STATUS_ACTIVE]);
        $importanceObjectsRaw = Importance::findAll(['importance_pack_id' => $this->importance_pack_id]);

        foreach ($importanceObjectsRaw as $importanceObject) {
            $importanceObjectsOrdered[$importanceObject->user_id] = $importanceObject;
        }

        $result = true;

        foreach ($users as $user) {
            if(!isset($importanceObjectsOrdered[$user->id])) {
                $importanceObject = new Importance();
                $importanceObject->user_id = $user->id;
                $importanceObject->importance_pack_id = $this->importance_pack_id;
                $importanceObject->importance = 0;

                $saveResult = $importanceObject->save();
                $result = $result && $saveResult;
            }
        }
        return $result;
    }
}
