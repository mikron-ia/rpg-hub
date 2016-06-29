<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "parameter_pack".
 *
 * @property string $parameter_pack_id
 * @property string $class
 * @property string $name
 *
 * @property Epic[] $epics
 * @property Parameter[] $parameters
 * @property Story[] $stories
 */
class ParameterPack extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'parameter_pack';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['class', 'name'], 'required'],
            [['class'], 'string', 'max' => 20],
            [['name'], 'string', 'max' => 80],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'description_pack_id' => Yii::t('app', 'PARAMETER_PACK_ID'),
            'class' => Yii::t('app', 'PARAMETER_PACK_CLASS'),
            'name' => Yii::t('app', 'PARAMETER_PACK_NAME'),
        ];
    }

    /**
     * @param string $className
     * @param int $id
     * @return ParameterPack Generated pack
     */
    static public function create($className, $id)
    {
        $pack = new ParameterPack();
        $pack->name = 'Parameters for ' . $className . ' #' . $id;

        $pack->save();
        $pack->refresh();

        return $pack;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEpics()
    {
        return $this->hasMany(Epic::className(), ['parameter_pack_id' => 'parameter_pack_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParameters()
    {
        return $this->hasMany(Parameter::className(), ['parameter_pack_id' => 'parameter_pack_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStories()
    {
        return $this->hasMany(Story::className(), ['parameter_pack_id' => 'parameter_pack_id']);
    }
}
