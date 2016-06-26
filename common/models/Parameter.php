<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "parameter".
 *
 * @property string $parameter_id
 * @property string $parameter_pack_id
 * @property string $code
 * @property string $lang
 * @property string $visibility
 * @property integer $position
 * @property string $content
 *
 * @property ParameterPack $parameterPack
 */
class Parameter extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'parameter';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['parameter_pack_id', 'position'], 'integer'],
            [['code', 'content'], 'required'],
            [['code', 'visibility'], 'string', 'max' => 20],
            [['lang'], 'string', 'max' => 5],
            [['content'], 'string', 'max' => 80],
            [['parameter_pack_id'], 'exist', 'skipOnError' => true, 'targetClass' => ParameterPack::className(), 'targetAttribute' => ['parameter_pack_id' => 'parameter_pack_id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'parameter_id' => Yii::t('app', 'Parameter ID'),
            'parameter_pack_id' => Yii::t('app', 'Parameter Pack ID'),
            'code' => Yii::t('app', 'Code'),
            'lang' => Yii::t('app', 'Lang'),
            'visibility' => Yii::t('app', 'Visibility'),
            'position' => Yii::t('app', 'Position'),
            'content' => Yii::t('app', 'Content'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParameterPack()
    {
        return $this->hasOne(ParameterPack::className(), ['parameter_pack_id' => 'parameter_pack_id']);
    }
}
