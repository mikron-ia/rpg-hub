<?php

namespace common\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "performed_action".
 *
 * @property string $id
 * @property string $user_id
 * @property string $operation
 * @property string $class
 * @property string $object_id
 * @property integer $performed_at
 *
 * @property User $user
 */
class PerformedAction extends ActiveRecord
{
    public static function tableName()
    {
        return 'performed_action';
    }

    public function rules()
    {
        return [
            [['user_id', 'operation', 'class', 'object_id', 'performed_at'], 'required'],
            [['user_id', 'object_id', 'performed_at'], 'integer'],
            [['operation', 'class'], 'string', 'max' => 80],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'user_id' => Yii::t('app', 'User ID'),
            'operation' => Yii::t('app', 'Operation'),
            'class' => Yii::t('app', 'Class'),
            'object_id' => Yii::t('app', 'Object ID'),
            'performed_at' => Yii::t('app', 'Performed At'),
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getUser():ActiveQuery
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}
