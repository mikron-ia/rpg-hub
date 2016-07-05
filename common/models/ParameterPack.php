<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "parameter_pack".
 *
 * @property string $parameter_pack_id
 * @property string $class
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
            'description_pack_id' => Yii::t('app', 'PARAMETER_PACK_ID'),
            'class' => Yii::t('app', 'PARAMETER_PACK_CLASS'),
        ];
    }

    /**
     * @param string $className
     * @return ParameterPack Generated pack
     */
    static public function create($className)
    {
        $pack = new ParameterPack();
        $pack->class = $className;

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

    /**
     * @param string $code
     * @return null|Parameter
     */
    public function getParameterByCode($code)
    {
        return Parameter::findOne([
            'parameter_pack_id' => $this->parameter_pack_id,
            'code' => $code
        ]);
    }

    /**
     * @param $code
     * @return null|string
     */
    public function getParameterValueByCode($code)
    {
        $parameter = $this->getParameterByCode($code);

        if ($parameter) {
            return $parameter->content;
        } else {
            return null;
        }
    }
}
